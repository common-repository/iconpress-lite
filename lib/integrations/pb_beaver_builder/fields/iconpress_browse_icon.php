<#
var uniqueID = data.name + '_' + Math.random().toString(36).substr(2, 9);
var isEmpty = !data.value ? 'is-empty' : '';
#>

<div class="iconpressBB-browseIcon-wrapper {{isEmpty}}" id="{{uniqueID}}">

	<div class="iconpressBB-browseIcon-iconWrapper" data-instance-id="{{uniqueID}}">
		<svg class="iconpress-icon js-iconpressBB-browseIcon-icon" aria-hidden="true" role="img">
			<use href="#{{ data.value }}" xlink:href="#{{ data.value }}"></use>
		</svg>
		<span><?php _e('No Icon Selected', 'iconpress') ?></span>
	</div>

	<button class="fl-builder-button fl-builder-button-primary fl-builder-button-small iconpressBB-browseIcon-insert" data-instance-id="{{uniqueID}}" data-add="<?php _e('Add Icon', 'iconpress'); ?>" data-change="<?php _e('Change Icon', 'iconpress'); ?>">
	</button>

	<button class="fl-builder-button fl-builder-button-small iconpressBB-browseIcon-remove">
		<?php _e('Remove', 'iconpress'); ?>
	</button>

	<input name="{{data.name}}" type="hidden" value="{{data.value}}">

</div>