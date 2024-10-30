<?php
// IconPress Entrance Animations
if ( !FLBuilderModel::is_builder_active() && $settings->animation == '' && isset( $settings->entrance_animation ) && ! empty($settings->entrance_animation) ) {
	echo '<div class="js-check-viewport entry-' . $settings->entrance_animation . '" ';
	// delay
	if( isset($settings->entrance_delay) && !empty( $settings->entrance_delay ) ){
		echo 'data-entry-delay="' . $settings->entrance_delay  . '"';
	}
	echo '>';
}

$iconWrapperClass = 'fl-icon-wrap iconpress-iconWrapper ';

if ( isset($settings->deko_hover_animation) && ! empty( $settings->deko_hover_animation ) ) {
	$iconWrapperClass .= 'iconPress-dekoAnimation-' . $settings->deko_hover_animation . ' ';
}
if ( isset($settings->full_size)  && 1 == $settings->full_size ) {
	$iconWrapperClass .= 'iconpress-iconWrapper--full ';
}
?>

<?php if ( ! empty( $settings->link ) ) : ?>
<a href="<?php echo $settings->link; ?>" class="<?php echo $iconWrapperClass?> iconpress-iconLink" target="<?php echo $settings->link_target; ?>" aria-label="link to <?php echo $settings->link; ?>"<?php echo ( '_blank' == $settings->link_target ) ? ' rel="noopener"' : '';?>>
<?php else: ?>
<div class="<?php echo $iconWrapperClass?>">
<?php endif; ?>

<?php

// Decorations

if( isset($settings->dc_style) && !empty($settings->dc_style) ){

	echo '<div class="iconPress-dekoWrapper">';

		// icon
		if( $settings->dc_style == 'icon' ){
			echo IconPress__getSvgIcon(array(
				'id' => $settings->deko_icon,
				'class' => 'iconPress-deko iconPress-deko-icon'
			));
		}
		
		echo '</div>';
	}
	
	?>

	<div class="iconPress-element-iconWrapper">
	<?php
		echo IconPress__getSvgIcon(array(
			'id' => $settings->icon,
			'class' => 'iconPress-element-icon'
		));
	?>
	</div>
<?php if ( ! empty( $settings->link ) ) : ?>
</a>
<?php else: ?>
</div>
<?php endif; ?>

<?php
// IconPress Entrance Animations
if ( !FLBuilderModel::is_builder_active() && $settings->animation == '' && isset( $settings->entrance_animation ) && ! empty($settings->entrance_animation) ) {
	echo '</div>';
}

?>