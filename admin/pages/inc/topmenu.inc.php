<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$helpMenu[] = [
	'title' => __('Knowledge Base', 'iconpress'),
	'url' => 'https://customers.iconpress.io/knowledge-base/'
];

?>
<ul class="ip-sideMenu">

	<li class="ip-dropDown ip-helpPanel">
		<a href="https://customers.iconpress.io/knowledge-base/" target="_blank" class="ip-link">
			<?php
			echo IconPress__getSvgIcon(['id' => 'iconpress-icon-help']);
			echo '<span>'. __( 'HELP', 'iconpress' ) .'</span>';
			?>
		</a>
		<div class="ip-dropDown-popover">
			<div class="ip-dropDown-popoverInner">
				<ul class="ip-helpMenu">
					<?php foreach ($helpMenu as $item) {
						echo '<li><a href="'.$item['url'].'" target="_blank" title="'.__('Will open in a new window', 'iconpress').'">'. $item['title'] .'</a></li>';
					} ?>
				</ul>
			</div>
		</div>
	</li>

	<?php

	do_action('iconpress/topmenu');

	?>

	<li>
		<a href="#" class="ip-refreshCache ip-u-tooltip ip-u-tooltip-bottom" id="ip-refreshCache" data-tooltip="<?php _e( 'Remove Browser Cache & Reload page', 'iconpress' ) ?>">
			<?php
			echo IconPress__getSvgIcon(['id' => 'iconpress-icon-refresh']);
			?>
		</a>
	</li>

	<li class="ip-logo">
		<a href="https://iconpress.io/?utm_source=plugin-top&utm_campaign=plugin&utm_medium=wp-dash" target="_blank">
			<img src="<?php echo ICONPRESSLITE_URI ?>assets/img/iconpress.svg"/></a>
	</li>
</ul>
