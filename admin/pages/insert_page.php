<?php

use IconPressLite\Helpers\FileSystem as FileSystem;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// @todo: check if nonce needed
if ( ! isset( $_GET['ip_nonce'] ) || ! wp_verify_nonce( $_GET['ip_nonce'], 'open_insert_panel' ) ) {
	_e( 'Nope!', 'iconpress' );
	return;
}

?>
<div class="wrap iconpress-page iconpress-appPage iconpressApp-components">
	<?php
	include( ICONPRESSLITE_DIR . 'admin/pages/inc/topmenu.inc.php' );
	//#! Check File System access
	FileSystem::checkWpFileSystem();
	?>
	<div class="iconpressApp " id="ip-icon-library" data-context="<?php echo esc_attr( $_GET['context'] ); ?>" data-active="myCollection"></div>
	<?php echo IconPress__getSvgIcon( [ 'id' => 'iconpress-icon-spinner', 'class' => 'iconpressApp-preloader' ] ); ?>
</div>
