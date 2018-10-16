jQuery(document).ready(function(){
	// Base init - will work for both page usage: create new from template or change template
	jQuery('.table-list-item .preset-select-btn').click(function(e){
		if(jQuery(this).hasClass('sup-promo')) {
			e.stopPropagation();
		} else {
			e.preventDefault();
		}
	});
	jQuery('.table-list-item.sup-promo').click(function(){
		toeRedirect(jQuery(this).find('.preset-select-btn').attr('href'), true);
	});
	// Init create new one from template, or change template for existing "substance" :)
	if(typeof(ptsOriginalTable) !== 'undefined') {	// Just changing template - for existing table
		ptsInitChangeTableDialog();
	} else {			// Creating new table
		ptsInitCreateTableDialog();
	}
	if(jQuery('.ptsTplPrevImg').size()) {	// If on creation page
		ptsAdjustPreviewSize();
		jQuery(window).resize(function(){
			ptsAdjustPreviewSize();
		});
	}

});

function ptsAdjustPreviewSize() {
	var shellWidth = parseInt(jQuery('.table-list').width())
	,	initialMaxWidth = 400
	,	startFrom = 860
	,	endFrom = 500;
	if(shellWidth < startFrom && shellWidth > endFrom) {
		jQuery('.ptsTplPrevImg').css('max-width', initialMaxWidth - Math.floor((startFrom - shellWidth) / 2));
	} else if(shellWidth < endFrom || shellWidth > startFrom) {
		jQuery('.ptsTplPrevImg').css('max-width', initialMaxWidth);
	}
}
function ptsInitChangeTableDialog() {
	var $container = jQuery('#ptsChangeTplWnd').dialog({
		modal:    true
	,	autoOpen: false
	,	width: 460
	,	height: 180
	,	buttons:  {
			OK: function() {
				jQuery('#ptsChangeTplForm').submit();
			}
		,	Cancel: function() {
				$container.dialog('close');
			}
		}
	});
	jQuery('.table-list-item[data-id='+ ptsOriginalTable.original_id+ ']')
		.addClass('active')
		.find('.preset-select-btn').each(function(){
			jQuery(this).html( jQuery(this).data('txt-active') );
	});
	jQuery('.table-list-item:not(.sup-promo)').click(function(){
		var id = jQuery(this).data('id');
		if(ptsOriginalTable.original_id == id) {
			var dialog = jQuery('<div />').html(toeLangPts('This is same template that was used for Table before')).dialog({
				modal:    true
			,	width: 480
			,	height: 180
			,	buttons: {
					OK: function() {
						dialog.dialog('close');
					}
				}
			,	close: function() {
					dialog.remove();
				}
			});
			return false;
		}
		jQuery('#ptsChangeTplForm').find('[name=id]').val( ptsOriginalTable.id );
		jQuery('#ptsChangeTplForm').find('[name=new_tpl_id]').val( id );
		jQuery('#ptsChangeTplNewLabel').html( jQuery(this).find('.ptsTplLabel').html() )
		jQuery('#ptsChangeTplMsg').html('');
		$container.dialog('open');
		return false;
	});
	jQuery('#ptsChangeTplForm').submit(function(){
		jQuery(this).sendFormPts({
			msgElID: 'ptsChangeTplMsg'
		,	onSuccess: function(res) {
				if(!res.error && res.data.edit_link) {
					toeRedirect( res.data.edit_link );
				}
			} 
		});
		return false;
	});
}
function ptsInitCreateTableDialog() {
	jQuery('.table-list-item:not(.sup-promo)').click(function(){
		jQuery('.table-list-item')
			.removeClass('active')
			.find('.preset-select-btn').each(function(){
				jQuery(this).html( jQuery(this).data('txt') );
			});
		jQuery(this).addClass('active')
			.find('.preset-select-btn').each(function(){
				jQuery(this).html( jQuery(this).data('txt-active') );
			});
		jQuery('#ptsCreateTableForm').find('[name=original_id]').val( jQuery(this).data('id') );
		return false;
	});
	jQuery('#ptsCreateTableForm').submit(function(){
		jQuery(this).sendFormPts({
			btn: jQuery(this).find('button')
		,	onSuccess: function(res) {
				if(!res.error && res.data.edit_link) {
					toeRedirect( res.data.edit_link );
				}
			} 
		});
		return false;
	});
}
function ptsTableRemoveRow(id, link) {
	var tblId = jQuery(link).parents('table.ui-jqgrid-btable:first').attr('id');
	if(confirm(toeLangPts('Are you sure want to remove "'+ ptsGetGridColDataById(id, 'label', tblId)+ '" Table?'))) {
		jQuery.sendFormPts({
			btn: link
		,	data: {mod: 'tables', action: 'remove', id: id}
		,	onSuccess: function(res) {
				if(!res.error) {
					jQuery('#'+ tblId).trigger( 'reloadGrid' );
				}
			}
		});
	}
}