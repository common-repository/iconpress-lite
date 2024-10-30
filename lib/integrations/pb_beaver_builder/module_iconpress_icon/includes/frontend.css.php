<?php

if ( $settings->three_d ) {
	$bg_grad_start = FLBuilderColor::adjust_brightness( $settings->bg_color, 30, 'lighten' );
	$border_color = FLBuilderColor::adjust_brightness( $settings->bg_color, 20, 'darken' );
}
if ( $settings->three_d && ! empty( $settings->bg_hover_color ) ) {
	$bg_hover_grad_start = FLBuilderColor::adjust_brightness( $settings->bg_hover_color, 30, 'lighten' );
	$border_hover_color = FLBuilderColor::adjust_brightness( $settings->bg_hover_color, 20, 'darken' );
}

?>
.fl-node-<?php echo $id; ?> .fl-module-content {
	line-height: 1;
<?php if ( ! empty( $settings->align ) ) : ?>
	text-align: <?php echo $settings->align; ?>
<?php endif; ?>
}

.fl-node-<?php echo $id; ?> .fl-icon-wrap {
	line-height: 1;
	text-align: center;
	vertical-align: middle;
	box-shadow:none;
	<?php if ( $settings->bg_color ) : // Rounded Styles ?>
	border-radius: 100%;
	padding: 0.3em;
	background: #<?php echo $settings->bg_color; ?>;
	<?php endif; ?>
	<?php if ( $settings->bg_color && $settings->three_d ) : // 3D Styles ?>
	background: linear-gradient(to bottom,  #<?php echo $bg_grad_start; ?> 0%,#<?php echo $settings->bg_color; ?> 100%); /* W3C *//* IE6-9 */
	border: 1px solid #<?php echo $border_color; ?>;
	<?php endif; ?>
}

.fl-node-<?php echo $id; ?> .iconpress-icon {
	font-size: <?php echo $settings->size; ?>px;

	<?php if ( isset($settings->icon_rotate) && $settings->icon_rotate != '' ) : ?>
	-webkit-transform: rotate(<?php echo $settings->icon_rotate; ?>deg);
	transform: rotate(<?php echo $settings->icon_rotate; ?>deg);
	<?php endif; ?>
}

.fl-node-<?php echo $id; ?> .iconpress-icon,
.fl-node-<?php echo $id; ?> .iconPress-element-iconWrapper {
	<?php if ( $settings->color ) : ?>
	color: #<?php echo $settings->color; ?>;
	<?php endif; ?>
}


.fl-node-<?php echo $id; ?> .fl-icon-wrap:hover,
.fl-node-<?php echo $id; ?> a:hover .fl-icon-wrap {
	<?php if ( ! empty( $settings->bg_hover_color ) ) : ?>
	background: #<?php echo $settings->bg_hover_color; ?>;
	<?php endif; ?>
	<?php if ( $settings->three_d && ! empty( $settings->bg_hover_color ) ) : // 3D Styles ?>
	background: linear-gradient(to bottom,  #<?php echo $bg_hover_grad_start; ?> 0%,#<?php echo $settings->bg_hover_color; ?> 100%); /* W3C */
	border: 1px solid #<?php echo $border_hover_color; ?>;
	<?php endif; ?>
	<?php if ( ! empty( $settings->hover_color ) ) : ?>
	color: #<?php echo $settings->hover_color; ?>;
	<?php endif; ?>
}

<?php if ( $global_settings->responsive_enabled && ( 'custom' == $settings->r_align ) ) : ?>
@media (max-width: <?php echo $global_settings->responsive_breakpoint; ?>px) {
	.fl-node-<?php echo $id; ?> .fl-module-content {
		text-align: <?php echo $settings->r_custom_align ?> !important;
	}
}
<?php endif; ?>

<?php

$deko_style = $deko_hover_style = '';
$deko_style .= $settings->deko_size ? 'font-size:' . (int) $settings->deko_size . 'px;' : '';
$deko_style .= $settings->deko_color ? 'color:#' . $settings->deko_color . ';' : '';
$deko_style .= $settings->deko_posX != '' ? 'left: calc( 50% - ( ( ' . $settings->deko_posX . 'em * -1 ) + 0.5em ) );' : '';
$deko_style .= $settings->deko_posY != '' ? 'top: calc( 50% - ( ( ' . $settings->deko_posY . 'em * -1 ) + 0.5em ) );' : '';
$deko_style .= $settings->deko_rotate ? 'transform: rotate(' . $settings->deko_rotate . 'deg);' : '';
$deko_style .= $settings->deko_opacity ? 'opacity:' . $settings->deko_opacity . ';' : '';
if( $deko_style ) { ?>
	.fl-node-<?php echo $id ?> .iconPress-deko {<?php echo $deko_style; ?>}
	<?php
}

// Deco hover
$deko_hover_style .= $settings->deko_color_hover ? 'color:#' . $settings->deko_color_hover . ';' : '';
if( $deko_hover_style ) { ?>
	.fl-node-<?php echo $id ?> .iconpress-iconWrapper:hover .iconPress-deko {<?php echo $deko_hover_style; ?>}
	<?php
}
