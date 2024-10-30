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
			'title' => __('My Collection page explained', 'iconpress'),
			'url' => 'https://customers.iconpress.io/kb/my-collection-page/'
		],
		[
			'title' => __('IconPress Shortcode', 'iconpress'),
			'url' => 'https://customers.iconpress.io/kb/iconpress-shortcode/'
		],
		[
			'title' => __('Insert from WordPress editor', 'iconpress'),
			'url' => 'https://customers.iconpress.io/kb/insert-from-wordpress-editor/'
		],
		[
			'title' => __('Page Builder Elements', 'iconpress'),
			'url' => 'https://customers.iconpress.io/kb/page-builder-elements/'
		],
		[
			'title' => __('Insert icons with PHP syntax', 'iconpress'),
			'url' => 'https://customers.iconpress.io/kb/insert-icons-with-php-syntax/'
		],
		[
			'title' => __('Insert icons as HTML code', 'iconpress'),
			'url' => 'https://customers.iconpress.io/kb/insert-icons-as-html-code/'
		],
	];
	include( ICONPRESSLITE_DIR . 'admin/pages/inc/topmenu.inc.php' );
	//#! Check File System access
	FileSystem::checkWpFileSystem();
	?>
	<div class="iconpressApp " id="ip-icon-library" data-context="management" data-active="myCollection"></div>
	<?php echo IconPress__getSvgIcon( [ 'id' => 'iconpress-icon-spinner', 'class' => 'iconpressApp-preloader' ] ); ?>
</div>
