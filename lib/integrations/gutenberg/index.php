<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * `wp-blocks`: includes block type registration and related functions.
 *
 * @since 1.0.0
 */
add_action( 'enqueue_block_assets', function () {
	// Styles.
	wp_enqueue_style(
		'iconpress-gtb-block-style-css', // Handle.
		ICONPRESSLITE_URI . 'lib/integrations/gutenberg/dist/blocks.style.build.css', // Block style CSS.
		array() // Dependency to include the CSS after it.
	);
} );


/**
 * Enqueue Gutenberg block assets for backend editor.
 *
 * `wp-blocks`: includes block type registration and related functions.
 * `wp-element`: includes the WordPress Element abstraction for describing the structure of your blocks.
 * `wp-i18n`: To internationalize the block's text.
 *
 * @since 1.0.0
 */
add_action( 'enqueue_block_editor_assets', function () {
	// Scripts.
	wp_enqueue_script(
		'iconpress-gtb-block-js', // Handle.
		ICONPRESSLITE_URI . 'lib/integrations/gutenberg/dist/blocks.build.js', // Block.build.js: We register the block here. Built with Webpack.
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor' ), // Dependencies, defined above.
		// filemtime( ICONPRESSLITE_URI . 'lib/integrations/gutenberg/dist/blocks.build.js', // Version: filemtime — Gets file modification time.
		true // Enqueue the script in the footer.
	);

	// Styles.
	wp_enqueue_style(
		'iconpress-gtb-block-editor-css', // Handle.
		ICONPRESSLITE_URI . 'lib/integrations/gutenberg/dist/blocks.editor.build.css', // Block editor CSS.
		array( 'wp-edit-blocks' ) // Dependency to include the CSS after it.
		// filemtime( ICONPRESSLITE_URI . 'lib/integrations/gutenberg/dist/blocks.editor.build.css' ) // Version: filemtime — Gets file modification time.
	);
} );


// Add custom block category
add_filter( 'block_categories', function( $categories, $post ) {
	return array_merge(
		$categories,
		array(
			array(
				'slug' => 'iconpress',
				'title' => __( 'IconPress', 'iconpress' ),
			),
		)
	);
}, 10, 2 );

// add gutenberg as support for iconpress
add_filter('iconpress/integrations/supported', function ($sup){
	$sup[] = 'gutenberg';
	return $sup;
} );
