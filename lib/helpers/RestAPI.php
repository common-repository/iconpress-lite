<?php

namespace IconPressLite\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class RestAPI
 *
 * Base class for Rest API requests
 *
 * @package IconPressLite\Helpers
 */
class RestAPI
{
	const ICONPRESS_NAMESPACE = 'iconpress/v1/';

	public static function registerRoutes()
	{

		/**
		 * IconPress Free icons endpoints
		 */
		register_rest_route( self::ICONPRESS_NAMESPACE, '/collections', [
			'methods' => \WP_REST_Server::READABLE,
			'callback' => [ '\\IconPressLite\\Database\\Collections', 'restAPI_getAllCollections' ],
		] );


		register_rest_route( self::ICONPRESS_NAMESPACE, '/collection', [
			'methods' => \WP_REST_Server::READABLE,
			'callback' => [ '\\IconPressLite\\Database\\Collections', 'restAPI_getCollection' ],
			'args' => [
				'collection_id' => [
					'validate_callback' => [ '\\IconPressLite\\Helpers\\Validator', 'notEmpty' ],
				],
			],
		] );

		register_rest_route( self::ICONPRESS_NAMESPACE, '/icons', [
			'methods' => \WP_REST_Server::READABLE,
			'callback' => [ '\\IconPressLite\\Database\\Icons', 'restAPI_getAllIcons' ],
			'args' => [
				'collection_identifier' => [
					'required' => true,
					'type' => 'string',
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => 'rest_validate_request_arg',
				],
			],
		] );

		register_rest_route( self::ICONPRESS_NAMESPACE, '/icons/search', [
			'methods' => \WP_REST_Server::READABLE,
			'callback' => [ '\\IconPressLite\\Database\\Icons', 'restAPI_searchIcons' ],
			'args' => [
				'q' => [
					'required' => true,
					'type' => 'string',
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => 'rest_validate_request_arg',
				],
			],
		] );

		register_rest_route( self::ICONPRESS_NAMESPACE, '/get_user_collections', [
			[
				'methods' => \WP_REST_Server::READABLE,
				'callback' => [ '\\IconPressLite\\Database\\Collections', 'restAPI_getUserCollections' ],
			]
		] );

		register_rest_route( self::ICONPRESS_NAMESPACE, '/ajax_save_collection', [
			[
				'methods' => \WP_REST_Server::CREATABLE,
				'callback' => [ '\\IconPressLite\\Database\\Collections', 'restAPI_ajaxSaveUserCollection' ],
				'args' => [
					'icons' => [
						'required' => true,
						'validate_callback' => [ get_class(), 'validateArray' ],
					],
				],
			]
		] );

		register_rest_route( self::ICONPRESS_NAMESPACE, '/delete_icon', [
			[
				'methods' => \WP_REST_Server::CREATABLE,
				'callback' => [ '\\IconPressLite\\Database\\Icons', 'restAPI_DeleteIcon' ],
				'args' => [
					'internal_id' => [
						'required' => true,
						'type' => 'string',
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => 'rest_validate_request_arg',
					],
				],
			],
		] );

		register_rest_route( self::ICONPRESS_NAMESPACE, '/delete_icons', [
			[
				'methods' => \WP_REST_Server::CREATABLE,
				'callback' => [ '\\IconPressLite\\Database\\Icons', 'restAPI_DeleteIcons' ],
				'args' => [
					'icons' => [
						'required' => true,
						'validate_callback' => [ get_class(), 'validateArray' ],
					],
				],
			],
		] );

		register_rest_route( self::ICONPRESS_NAMESPACE, '/get_icon_info', [
			[
				'methods' => \WP_REST_Server::READABLE,
				'callback' => [ '\\IconPressLite\\Database\\Icons', 'restAPI_getIconInfo' ],
				'args' => [
					'id' => [
						'required' => true,
						'type' => 'integer',
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => 'rest_validate_request_arg',
					],
					'type' => [
						'required' => false,
						'type' => 'string',
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => 'rest_validate_request_arg',
					],
				],
			],
		] );

		register_rest_route( self::ICONPRESS_NAMESPACE, '/get_svg_sprite_content', [
			[
				'methods' => \WP_REST_Server::READABLE,
				'callback' => [ '\\IconPressLite\\Database\\Collections', 'restAPI_getSvgSpriteContent' ],
			]
		] );

		register_rest_route( self::ICONPRESS_NAMESPACE, '/export_collection', [
			[
				'methods' => \WP_REST_Server::READABLE,
				'callback' => [ '\\IconPressLite\\Helpers\\Portability', 'restAPI_exportCollection' ],
			]
		] );

		register_rest_route( self::ICONPRESS_NAMESPACE, '/delete_export', [
			[
				'methods' => \WP_REST_Server::READABLE,
				'callback' => [ '\\IconPressLite\\Helpers\\Portability', 'restAPI_deleteExport' ],
				'args' => [
					'filename' => [
						'required' => true,
						'type' => 'string',
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => 'rest_validate_request_arg',
					]
				],
			]
		] );

		register_rest_route( self::ICONPRESS_NAMESPACE, '/restore', [
			[
				'methods' => \WP_REST_Server::CREATABLE,
				'callback' => [ '\\IconPressLite\\Helpers\\Portability', 'restAPI_restore' ],
				'args' => [
					// 'file' => [
					// 	'required' => true,
					// 	// 'type' => 'string',
					// 	// 'sanitize_callback' => 'sanitize_text_field',
					// 	// 'validate_callback' => 'validateArray',
					// ],
					'overwrite' => [
						'required' => true,
						'type' => 'boolean',
						'default' => false,
						'sanitize_callback' => [ get_class(), 'sanitizeBool' ],
						'validate_callback' => 'rest_validate_request_arg',
					]
				],
			]
		] );

		register_rest_route( self::ICONPRESS_NAMESPACE, '/import_icons', [
			[
				'methods' => \WP_REST_Server::READABLE,
				'callback' => [ '\\IconPressLite\\Helpers\\Importer', 'restAPI_importDefaultData' ],
			]
		] );

	}


	/**
	 * Internal REST API method to check whether or not the current user is allowed to perform a specific task
	 * @return bool
	 */
	public static function isUserAllowed()
	{
		return current_user_can( \IconPressLite\Base::CAPABILITY );
	}

	public static function validateNumber( $param, $request, $key )
	{
		return is_numeric( $param );
	}

	public static function validateArray( $param, $request, $key )
	{
		return is_array( $param ) && ! empty( $param );
	}

	public static function sanitizeBool( $string ) {
		return is_bool( $string ) ? $string : ( 'yes' === $string || 1 === $string || 'true' === $string || '1' === $string );
	}
}
