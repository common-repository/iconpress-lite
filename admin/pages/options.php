<?php

use IconPressLite\Base;
use IconPressLite\Helpers\Validator;
use IconPressLite\Helpers\FileSystem;
use IconPressLite\Helpers\Option;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$options = get_option( Option::getOptionName( Option::PLUGIN_OPTIONS ), [] );

$options['grid_listing_default_color'] = ( empty( $options['grid_listing_default_color'] ) ? '#525252' : $options['grid_listing_default_color'] );
$options['grid_customization_color'] = ( empty( $options['grid_customization_color'] ) ? '#525252' : $options['grid_customization_color'] );
$options['grid_listing_default_size'] = ( empty( $options['grid_listing_default_size'] ) ? '32' : $options['grid_listing_default_size'] );
$options['integrations'] = ( empty( $options['integrations'] ) ? [ 'customizer', 'wpbakery', 'elementor', 'beaver-builder', 'gutenberg' ] : $options['integrations'] );
$options['enable_wpeditor_btn'] = isset( $options['enable_wpeditor_btn'] ) ? $options['enable_wpeditor_btn'] : '1';
$options['user_roles'] = ( empty( $options['user_roles'] ) ? [ 'administrator' ] : $options['user_roles'] );
$options['enable_dummy_customizer_btn'] = isset( $options['enable_dummy_customizer_btn'] ) ? $options['enable_dummy_customizer_btn'] : '0';
$options['enable_lock'] = isset( $options['enable_lock'] ) ? $options['enable_lock'] : '1';
$options['enable_media_library'] = isset( $options['enable_media_library'] ) ? $options['enable_media_library'] : '1';
$options['enable_debug'] = isset( $options['enable_debug'] ) ? $options['enable_debug'] : '0';
$options['dequeue_icons'] = isset( $options['dequeue_icons'] ) ? $options['dequeue_icons'] : '';

$options['load_icons_as'] = isset( $options['load_icons_as'] ) ? $options['load_icons_as'] : 'ajax';
$options['system_frontend'] = isset( $options['system_frontend'] ) ? $options['system_frontend'] : '0';

?>
<div class="wrap iconpress-page iconpress-pageOptions iconpressApp-components">

	<?php
	$helpMenu = [
		[
			'title' => __( 'IconPress options explained', 'iconpress' ),
			'url' => 'https://customers.iconpress.io/kb/iconpress-options/'
		]
	];
	include( ICONPRESSLITE_DIR . 'admin/pages/inc/topmenu.inc.php' ); ?>

	<h1 class="iconpress-pageTitle"><?php _e( 'IconPress - Options', 'iconpress' ); ?></h1>
	<?php
	//#! Check File System access
	FileSystem::checkWpFileSystem();
	?>

	<?php
	/*
	 * Re-scan collections
	 */
	if ( isset( $_REQUEST['iconpress_rescan'] ) ) {
		if ( wp_verify_nonce( $_REQUEST[IconPressLite\Base::NONCE_NAME], IconPressLite\Base::NONCE_ACTION ) ) {
			$result = \IconPressLite\Helpers\Importer::importDefaultData();
			if ( $result ) {
				echo '<div class="notice notice-success">';
				echo '<p>' . __( 'Collections imported/updated successfully.', 'iconpress' ) . '</p>';
				echo '</div>';
			}
			else {
				echo '<div class="notice notice-error">';
				echo '<p>' . __( 'An error occurred while re-scanning collections.', 'iconpress' ) . '</p>';
				echo '</div>';
			}
		}
	}

	//#! Validate options save
	if ( 'POST' == strtoupper( $_SERVER['REQUEST_METHOD'] ) ) {
		$result = Validator::validatePluginOptionsSave( $_POST );
		if ( ! empty( $result ) ) {
			echo '<div class="notice notice-error">';
			foreach ( $result as $error ) {
				echo "<p>{$error}</p>";
			}
			echo '</div>';
		}
		else {
			//#! Options save
			$options['grid_listing_default_color'] = ( empty( $_POST['grid_listing_default_color'] ) ? '#525252' : $_POST['grid_listing_default_color'] );
			$options['grid_customization_color'] = ( empty( $_POST['grid_customization_color'] ) ? '#525252' : $_POST['grid_customization_color'] );
			$options['grid_listing_default_size'] = ( empty( $_POST['grid_listing_default_size'] ) ? '32' : $_POST['grid_listing_default_size'] );
			$options['integrations'] = ( empty( $_POST['integrations'] ) ? [ 'customizer', 'wpbakery', 'elementor', 'beaver-builder', 'gutenberg' ] : $_POST['integrations'] );
			$options['enable_wpeditor_btn'] = isset( $_POST['enable_wpeditor_btn'] ) ? $_POST['enable_wpeditor_btn'] : '0';
			$options['user_roles'] = ( empty( $_POST['user_roles'] ) ? [ 'administrator' ] : $_POST['user_roles'] );
			$options['enable_dummy_customizer_btn'] = isset( $_POST['enable_dummy_customizer_btn'] ) ? $_POST['enable_dummy_customizer_btn'] : '0';
			$options['enable_lock'] = isset( $_POST['enable_lock'] ) && $_POST['enable_lock'] == '1' ? '1' : '0';
			$options['enable_media_library'] = isset( $_POST['enable_media_library'] ) && $_POST['enable_media_library'] == '1' ? '1' : '0';
			$options['enable_debug'] = isset( $_POST['enable_debug'] ) ? $_POST['enable_debug'] : '0';
			$options['dequeue_icons'] = isset( $_POST['dequeue_icons'] ) ? stripslashes( $_POST['dequeue_icons'] ) : '';
			$options['load_icons_as'] = isset( $_POST['load_icons_as'] ) ? $_POST['load_icons_as'] : 'ajax';
			$options['system_frontend'] = isset( $_POST['system_frontend'] ) ? $_POST['system_frontend'] : '0';

			update_option( Option::getOptionName( Option::PLUGIN_OPTIONS ), $options );
			IconPressLite\Base::getInstance()->updateUserAccessRoles( $options['user_roles'] );

			echo '<div class="notice notice-success">';
			echo '<p>' . __( 'Options saved.', 'iconpress' ) . '</p>';
			echo '</div>';
		}
	}
	?>

	<div class="iconpress-wrap-inner">
		<form method="post">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<label for="grid_listing_default_color"><?php _e( 'Icons color in grid listing.', 'iconpress' ); ?></label>
						</th>
						<td>
							<input type="text" id="grid_listing_default_color" name="grid_listing_default_color"
							       class="regular-text ip-colorField"
							       data-default-color="#525252"
							       value="<?php echo esc_attr( $options['grid_listing_default_color'] ); ?>"/>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="grid_listing_default_size"><?php _e( 'Default size in grid listing.', 'iconpress' ); ?></label>
						</th>
						<td>
							<select id="grid_listing_default_size" name="grid_listing_default_size">
								<option value="16" <?php selected( '16', $options['grid_listing_default_size'] ); ?>><?php _e( '16px', 'iconpress' ); ?></option>
								<option value="24" <?php selected( '24', $options['grid_listing_default_size'] ); ?>><?php _e( '24px', 'iconpress' ); ?></option>
								<option value="32" <?php selected( '32', $options['grid_listing_default_size'] ); ?>><?php _e( '32px', 'iconpress' ); ?></option>
								<option value="64" <?php selected( '64', $options['grid_listing_default_size'] ); ?>><?php _e( '64px', 'iconpress' ); ?></option>
								<option value="128" <?php selected( '128', $options['grid_listing_default_size'] ); ?>><?php _e( '128px', 'iconpress' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="grid_customization_color"><?php _e( 'Icons color for customization options.', 'iconpress' ); ?></label>
						</th>
						<td>
							<input type="text" id="grid_customization_color" name="grid_customization_color"
							       class="regular-text ip-colorField"
							       data-default-color="#525252"
							       value="<?php echo esc_attr( $options['grid_customization_color'] ); ?>"/>
							<p class="description">
								<?php _e( "Select a color which is used as default when customizing an icon from your collection.", 'iconpress' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<?php _e( 'How to load icons?', 'iconpress' ); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><span><?php _e( 'How to load icons?', 'iconpress' ); ?></span></legend>

								<input type="radio" id="load_icons_as_sprite" name="load_icons_as" value="inline" <?php checked( 'inline', $options['load_icons_as'] ); ?>/>
								<label for="load_icons_as_sprite">
									<?php _e( 'Inline', 'iconpress' ); ?>
								</label>
								<br>
								<input type="radio" id="load_icons_as_ajax" name="load_icons_as"
									title="<?php _e( 'Ajax.', 'iconpress' ); ?>"
									value="ajax" <?php checked( 'ajax', $options['load_icons_as'] ); ?>/>
								<label for="load_icons_as_ajax">
									<?php _e( 'AJAX', 'iconpress' ); ?>
								</label>
							</fieldset>
						</td>
					</tr>


					<tr>
						<th scope="row">
							<label><?php _e( 'Load system icons into frontend?', 'iconpress' ); ?></label>
						</th>
						<td>
							<fieldset>
								<input type="checkbox" id="system_frontend" name="system_frontend" value="1" <?php checked( '1', $options['system_frontend'] ); ?>/>
								<label for="system_frontend"><?php _e( 'Yes', 'iconpress' ); ?></label>
							</fieldset>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label><?php _e( 'Integrations:', 'iconpress' ); ?></label>
						</th>
						<td>
							<fieldset>
								<input type="checkbox" id="integrations_customizer" name="integrations[]" value="customizer" <?php echo in_array( 'customizer', $options['integrations'] ) ? 'checked="checked"' : ''; ?> />
								<label for="integrations_customizer">
									<?php _e( 'WordPress Customizer', 'iconpress' ); ?>
								</label>
								<br>

								<input type="checkbox" id="integrations_wpb" name="integrations[]" value="wpbakery" <?php echo in_array( 'wpbakery', $options['integrations'] ) ? 'checked="checked"' : ''; ?> />
								<label for="integrations_wpb">
									<?php echo sprintf( '%s [<a href="https://wpbakery.com/" target="_blank">%s</a>]', __( 'WPBakery Page Builder', 'iconpress' ), __( 'visit', 'iconpress' ) ); ?>
								</label>
								<br>

								<input type="checkbox" id="integrations_elementor" name="integrations[]" value="elementor" <?php echo in_array( 'elementor', $options['integrations'] ) ? 'checked="checked"' : ''; ?> />
								<label for="integrations_elementor">
									<?php echo sprintf( '%s [<a href="https://wordpress.org/plugins/elementor/" target="_blank">%s</a>]', __( 'Elementor', 'iconpress' ), __( 'visit', 'iconpress' ) ); ?>
								</label>
								<br>

								<input type="checkbox" id="integrations_beaverbuilder" name="integrations[]" value="beaver-builder" <?php echo in_array( 'beaver-builder', $options['integrations'] ) ? 'checked="checked"' : ''; ?> />
								<label for="integrations_beaverbuilder">
									<?php echo sprintf( '%s [<a href="https://wordpress.org/plugins/beaver-builder-lite-version/" target="_blank">%s</a>]', __( 'Beaver Builder', 'iconpress' ), __( 'visit', 'iconpress' ) ); ?>
								</label>
								<br>

								<input type="checkbox" id="integrations_gutenberg" name="integrations[]" value="gutenberg" <?php echo in_array( 'gutenberg', $options['integrations'] ) ? 'checked="checked"' : ''; ?> />
								<label for="integrations_gutenberg">
									<?php echo sprintf( '%s [<a href="https://wordpress.org/plugins/gutenberg/" target="_blank">%s</a>]', __( 'GutenBerg', 'iconpress' ), __( 'visit', 'iconpress' ) ); ?>
								</label>
								<br>

								<input type="checkbox" id="integrations_divi" name="integrations[]" class="" value="divi" disabled/>
								<label for="integrations_divi">
									<?php echo __( 'Divi Builder <em>(Coming soon)</em>', 'iconpress' ); ?>
								</label>
								<br>

								<input type="checkbox" id="more_soon" disabled/>
								<label for="more_soon">
									<?php _e( 'More Builders Soon (Visual Composer, Divi Builder, King Composer, SiteOrigin Builder, etc.)', 'iconpress' ); ?>
								</label>

							</fieldset>
							<p class="description"><?php _e( "By default, the elements of these builders will load automatically, however, if you don't plan on using some of them, simply disable and skip loading their functionality.", 'iconpress' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label><?php _e( 'Enable editor "Insert Icon" button?', 'iconpress' ); ?></label>
						</th>
						<td>
							<fieldset>
								<input type="checkbox" id="enable_wpeditor_btn" name="enable_wpeditor_btn" value="1" <?php checked( '1', $options['enable_wpeditor_btn'] ); ?>/>
								<label for="enable_wpeditor_btn"><?php _e( 'Enable', 'iconpress' ); ?></label>
							</fieldset>
							<p class="description"><?php _e( 'Enabling this will show an insert icon button right above the WordPress TinyMCE Editor,<br>which will allow you to insert an icon via shortcode.', 'iconpress' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="user_roles"><?php _e( 'User roles', 'iconpress' ); ?></label>
						</th>
						<td>
							<fieldset>
								<?php
								$userRoles = Base::wp_roles_array();
								foreach ( $userRoles as $entry ) {
									$role = $entry['role'];
									$name = $entry['name'];

									$checked = ( ( in_array( $role, $options['user_roles'] ) || 'administrator' == $role ) ? 'checked="checked"' : '' );
									$disabled = ( $role == 'administrator' ? 'disabled="disabled"' : '' );
									?>
									<label for="role_<?php echo esc_attr( $role ); ?>">
										<input type="checkbox" id="role_<?php echo esc_attr( $role ); ?>" name="user_roles[]" class=""
										       value="<?php echo esc_attr( $role ); ?>" <?php echo $checked; ?> <?php echo $disabled; ?>/>
										<?php echo esc_html( $name ); ?>
									</label>
									<br>
									<?php
								}
								?>
							</fieldset>
							<p class="description"><?php _e( 'Choose the roles allowed to use this plugin.', 'iconpress' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label><?php _e( 'Dequeue font-icons', 'iconpress' ); ?></label>
						</th>
						<td>
							<fieldset>
								<textarea id="dequeue_icons" name="dequeue_icons" cols="70"><?php echo stripslashes( wp_specialchars_decode( $options['dequeue_icons'] ) ); ?></textarea>
								<label for="dequeue_icons"></label>
							</fieldset>
							<p class="description"><?php
								echo sprintf(
									__( 'Enter here the list of IDs of the font-icons you want to deregister from your theme or plugins. Separate entries with comma. Eg: "font-awesome, font_awesome,". ', 'iconpress' ),
									'#', '_self'
								); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label><?php _e( 'Backup Collection', 'iconpress' ); ?></label>
						</th>
						<td>
							<fieldset class="ip-optionsFieldset">
								<button class="ip-btn ip-btn--lined ip-btn--black js-options-export-backup"><?php _e( 'Export collection', 'iconpress' ) ?></button>
								<span class="spinner"></span>
							</fieldset>
							<p class="description clear"><?php _e( "Export a backup of your collection.", 'iconpress' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label><?php _e( 'Restore Collection', 'iconpress' ); ?></label>
						</th>
						<td>
							<fieldset class="ip-optionsFieldset">
								<button class="ip-btn ip-btn--lined ip-btn--black js-options-import-backup"><?php _e( 'Import / Restore collection', 'iconpress' ) ?></button>
								<input id="restore-uploader-file" type="file" hidden>
								<span class="spinner"></span>
							</fieldset>
							<fieldset class="ip-optionsFieldset">
								<input type="checkbox" id="overwrite_import" name="overwrite_import" value="1"/>
								<label for="overwrite_import"><?php _e( 'Overwrite duplicates?', 'iconpress' ); ?></label>
							</fieldset>
							<p class="description clear"><?php _e( "Restore a backup of your collection.", 'iconpress' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label><?php _e( 'Lock icons per user?', 'iconpress' ); ?></label>
						</th>
						<td>
							<fieldset>
								<input type="checkbox" id="enable_lock" name="enable_lock" value="1" <?php checked( '1', $options['enable_lock'] ); ?>/>
								<label for="enable_lock"><?php _e( 'Enable', 'iconpress' ); ?></label>
							</fieldset>
							<p class="description"><?php _e( "This option will lock icons to be removed from the custom collection, if the icon was added from another user account.", 'iconpress' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label><?php _e( 'Add Icons To Media Library', 'iconpress' ); ?></label>
						</th>
						<td>
							<fieldset>
								<input type="checkbox" id="enable_media_library" name="enable_media_library" value="1" <?php checked( '1', $options['enable_media_library'] ); ?>/>
								<label for="enable_media_library"><?php _e( 'Enable', 'iconpress' ); ?></label>
							</fieldset>
							<p class="description"><?php _e( "By default icons are imported into Media Library too.", 'iconpress' ); ?></p>



						</td>
					</tr>

					<tr>
						<th scope="row">
							<label><?php _e( 'Enable debug mode?', 'iconpress' ); ?></label>
						</th>
						<td>
							<fieldset>
								<input type="checkbox" id="enable_debug" name="enable_debug" value="1" <?php checked( '1', $options['enable_debug'] ); ?>/>
								<label for="enable_debug"><?php _e( 'Enable', 'iconpress' ); ?></label>
							</fieldset>
							<p class="description"><?php _e( "Should always be disabled unless you're in development mode.", 'iconpress' ); ?></p>
							<?php $rescanUrl = wp_nonce_url( add_query_arg( [ 'page' => \IconPressLite\Base::PLUGIN_SLUG . '_options', 'iconpress_rescan' => true ], admin_url( 'admin.php' ) ), IconPressLite\Base::NONCE_ACTION, IconPressLite\Base::NONCE_NAME ); ?>
							<p>
								<a href="<?php echo $rescanUrl; ?>" id="js-rescanCollections" title="<?php _e( "Click to re-import all collections", 'iconpress' ); ?>"><?php _e( "Re-scan collections", 'iconpress' ); ?></a>
							</p>

						</td>
					</tr>


				</tbody>
			</table>
			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save options', 'iconpress' ); ?>">
			</p>
			<?php
			wp_nonce_field( IconPressLite\Base::NONCE_ACTION, IconPressLite\Base::NONCE_NAME );
			?>
		</form>
	</div>
</div>
