<?php
use IconPressLite\Helpers\FileSystem as FileSystem;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap iconpress-page iconpress-appPage iconpressApp-components">
	<?php
	$helpMenu = [
		[
			'title' => __('Adding & Saving icons into your collection', 'iconpress'),
			'url' => 'https://customers.iconpress.io/kb/adding-saving-icons-into-your-collection/'
		],
	];
	include( ICONPRESSLITE_DIR . 'admin/pages/inc/topmenu.inc.php' );
	//#! Check File System access
	FileSystem::checkWpFileSystem();
	?>
	<div class="iconpressApp" id="ip-icon-library" data-context="management" data-active="iconPress"></div>
	<?php echo IconPress__getSvgIcon( [ 'id' => 'iconpress-icon-spinner', 'class'=> 'iconpressApp-preloader' ] ); ?>
	<?php
	?>
</div>
