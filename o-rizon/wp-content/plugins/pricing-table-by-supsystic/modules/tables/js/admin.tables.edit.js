var g_ptsTableBlock = null;
var ptsBlockCssEditor = (function(){
	var object = {},
		cssField = jQuery('#ptsBbCssInp').get(0),
		editBlock = null;
	var cssEditor = CodeMirror.fromTextArea(cssField, {
			mode: 'css'
		,	lineWrapping: true
		,	lineNumbers: true
		,	matchBrackets: true
	    ,	autoCloseBrackets: true
	    ,	autofocus: false
	});
	
	cssField.CodeMirrorEditor = cssEditor;
	
	var $container = jQuery('#ptsTableInitEditCssDlg').dialog({
			modal:    true
		,	autoOpen: false
		,	width: '90%'
		,	height: (jQuery(window).height() / 10) * 8.5
		,	show: {
	            effect: "fade",
	            duration: 1000
	        }
	    ,   hide: {
	            effect: "fade",
	            duration: 500
	        }
		,	buttons:  [
				{
					text: 'Ok'
				,	class: 'button button-sup-small'
				,	click: function() {
					var newCode = cssField.CodeMirrorEditor.getValue();

					if (editBlock != null) {
						editBlock._data.css = newCode;
						editBlock._rebuildCss();
						editBlock.contentChanged();
					}
					
					$container.dialog('close');
					editBlock = null;
				}
			}
			,	{
					text: 'Cancel'
				,	class: 'button button-sup-small'
				,	click: function() {
						$container.dialog('close');
						editBlock = null;
					}
				}
			]
		});

	// methods
	object.show = function (block) {
		cssField.CodeMirrorEditor.setValue(block._data.css);
		editBlock = block;
		$container.dialog('open');
		cssField.CodeMirrorEditor.refresh();
	};

	return object;
})();

jQuery(document).ready(function(){
	g_ptsTableBlock = ptsGetFabric().getBlocks()[0];

	jQuery('.supsystic-plugin .ptsTableSaveBtn').bind('click', function(){
		_ptsSaveCanvas(false, true);
		return false;
	});

	// If user has been deleted a cell, then system add new ptsEl by click.
	(function($){
		var root = '#ptsCanvas';
		$(root + ' .ptsCell,' +
			root + ' .ptsColDesc,' +
			root + ' .ptsColFooter,' +
			root + ' .ptsColHeader').on('click', function(){

			var self = $(this);
			if (0 == self.children().length
				|| (self.hasClass('ptsCell') && 1 == self.children().length) ) {

				var block = false;
				for(var i = 0; i < ptsTables.length; i++) {
					block = ptsGetFabric().getByViewId( ptsTables[i].view_id );
					if(block) {
						break;
					}
				}
				block.addPtsEl(self);
            	//#212
				if(self.hasClass('ptsColFooter')) {
                    self.prev().addClass('ptsColFooter')
                }
				self.remove();
			}
		});
	})(jQuery);

	// Delete btn init
	jQuery('.ptsTableRemoveBtn').click(function(){
		if(confirm(toeLangPts('Are you sure want to remove this Table?'))) {
			jQuery.sendFormPts({
				btn: this
			,	data: {mod: 'tables', action: 'remove', id: ptsGetFabric().getBlocks()[0].get('id')}	//[0] - we have only one block in this plugin - table block
			,	onSuccess: function(res) {
					if(!res.error) {
						toeRedirect( ptsAddNewUrl );
					}
				}
			});
		}
		return false;
	});
	jQuery('.ptsAddColumnBtn').click(function(){
		for(var i = 0; i < ptsTables.length; i++) {
			var block = ptsGetFabric().getByViewId( ptsTables[i].view_id );
			if(block) {
				block.addColumn();
				_ptsSetColsNumSetting(block);
			}
		}
		return false;
	});
	jQuery('.ptsAddRowBtn').click(function(){
		for(var i = 0; i < ptsTables.length; i++) {
			var block = ptsGetFabric().getByViewId( ptsTables[i].view_id );
			if(block) {
				block.addRow();
				_ptsSetRowsNumSetting(block);
			}
		}
		return false;
	});
	for(var i = 0; i < ptsTables.length; i++) {
		var block = ptsGetFabric().getByViewId( ptsTables[i].view_id );
		_ptsSetColsNumSetting(block);
		_ptsSetRowsNumSetting(block);
	}
	// Bg color input init
	_ptsCreateColorPickerFrom('.ptsColorPickBgColor', function(pcColor) {
		_ptsCheckBgColorNotice();
		_ptsGetTableBlock().setParam('bg_color', pcColor.formatted);
		_ptsGetTableBlock()._rebuildCss();
	});
	// Enable / disable description column
	jQuery('.ptsTableSettingsShell input[name="params[enb_desc_col]"]').change(function(){
		_ptsGetTableBlock().switchDescCol( jQuery(this).attr('checked') );
		_ptsGetTableBlock().contentChanged();
	});
	// Columns width manipulation
	jQuery('.ptsTableSettingsShell input[name="params[col_width]"]').change(function(){
		_ptsGetTableBlock().setColsWidth( jQuery(this).val() );
		_ptsGetTableBlock().contentChanged();
	});
	// Columns vertical padding manipulation
	jQuery('.ptsTableSettingsShell input[name="params[vert_padding]"]').change(function(){
		_ptsGetTableBlock().setTableVertPadding( jQuery(this).val() );
		_ptsGetTableBlock().contentChanged();
	});
	// Table calc width type change
	jQuery('.ptsTableSettingsShell input[name="params[calc_width]"]').change(function(){
		if(!jQuery(this).attr('checked')) return;
		_ptsGetTableBlock().setCalcWidth( jQuery(this).val() );
		_ptsGetTableBlock().contentChanged();
	});
	// Table width manipulation
	jQuery('.ptsTableSettingsShell input[name="params[table_width]"]').change(function(){
		_ptsGetTableBlock().setTableWidth( jQuery(this).val() );
		_ptsGetTableBlock().contentChanged();
	});
    // Don't allow users to set more then 100% width
    jQuery('.ptsTableSettingsShell input[name="params[table_width]"]').keyup(function(){
        var measureType = jQuery('[name="params[table_width_measure]"]:checked').val();
        if(measureType == '%') {
            var currentValue = parseInt( jQuery(this).val() );
            if(currentValue > 100) {
                jQuery(this).val( 100 );
            }
        }
    });
	jQuery('.ptsTableSettingsShell input[name="params[table_width_measure]"]').change(function(){
		if(!jQuery(this).attr('checked')) return;
		if(_ptsGetTableBlock().getParam('calc_width') !== 'table') return;
		var newMeasure = jQuery(this).val();
		_ptsGetTableBlock().setTableWidth( false, jQuery(this).val() );
		if(newMeasure == '%') {
			var width = parseFloat(jQuery('.ptsTableSettingsShell input[name="params[table_width]"]').val());
			if(width > 100) {
				jQuery('.ptsTableSettingsShell input[name="params[table_width]"]').val( 100 ).trigger('change');
			}
		}
	});
	// Hover effect animation check
	jQuery('.ptsTableSettingsShell input[name="params[enb_hover_animation]"]').change(function(){
		jQuery(this).attr('checked')
			? _ptsGetTableBlock()._initHoverEffect()
			: _ptsGetTableBlock()._disableHoverEffect();
	});
	jQuery('.ptsTableSettingsShell select[name="params[text_align]"]').change(function(){
		var aligns = ['left', 'right', 'center'];

		for (var i in aligns)
			_ptsGetTableBlock().$().removeClass('ptsAlign_' + aligns[i]);

		_ptsGetTableBlock().$().addClass('ptsAlign_' + jQuery(this).val());
		_ptsGetTableBlock().setParam('text_align', jQuery(this).val());
	});
	jQuery('.ptsTableSettingsShell select[name="params[table_align]"]').change(function(){
		var aligns = ['left', 'right', 'center', 'none'];

		for (var i in aligns)
			_ptsGetTableBlock().$().removeClass('ptsTableAlign_' + aligns[i]);

		_ptsGetTableBlock().$().addClass('ptsTableAlign_' + jQuery(this).val());
		_ptsGetTableBlock().setParam('table_align', jQuery(this).val());
	});
	// Editable PopUp title
	jQuery('#ptsTableEditableLabelShell').click(function(){
		var isEdit = jQuery(this).data('edit-on');
		if(!isEdit) {
			var $labelHtml = jQuery('#ptsTableEditableLabel')
			,	$labelTxt = jQuery('#ptsTableEditableLabelTxt');
			$labelTxt.val( $labelHtml.text() );
			$labelHtml.hide( g_ptsAnimationSpeed );
			$labelTxt.show( g_ptsAnimationSpeed, function(){
				jQuery(this).data('ready', 1).focus();
			});
			jQuery(this).data('edit-on', 1);
		}
	});
	jQuery('#ptsTableEditableLabelTxt').blur(function(){
		ptsFinishEditTableLabel( jQuery(this).val() );
	}).keydown(function(e){
		if(e.keyCode == 13) {	// Enter pressed
			ptsFinishEditTableLabel( jQuery(this).val() );
		}
	});
	// Font family for Table manipulations
	jQuery('.ptsTableSettingsShell select[name="params[font_family]"]').change(function(){
		_ptsGetTableBlock()._setFont( jQuery(this).val() );
	});
	// Text color input init
	_ptsCreateColorPickerFrom('.ptsColorPickTextColor', function(pcColor) {
		_ptsGetTableBlock().setParam('text_color', pcColor.formatted);
		_ptsGetTableBlock()._rebuildCss();
	});
	// Header text color
	_ptsCreateColorPickerFrom('.ptsColorPickTextColorHeader', function(pcColor) {
		_ptsGetTableBlock().setParam('text_color_header', pcColor.formatted);
		_ptsGetTableBlock()._rebuildCss();
	});
	// Desc cell text color
	_ptsCreateColorPickerFrom('.ptsColorPickTextColorDesc', function(pcColor) {
		_ptsGetTableBlock().setParam('text_color_desc', pcColor.formatted);
		_ptsGetTableBlock()._rebuildCss();
	});
	// End/dsbl Head row check
	jQuery('.ptsTableSettingsShell input[name="params[enb_head_row]"]').change(function(){
		_ptsGetTableBlock()._switchHeadRow({ state: jQuery(this).prop('checked') });	//"!" here is because option is actually for hide
		var state = jQuery(this).prop('checked') === true ? '1' : '0';
        _ptsGetTableBlock().setParam('enb_head_row', state);
	});
	// End/dsbl Desc row check
	jQuery('.ptsTableSettingsShell input[name="params[enb_desc_row]"]').change(function(){
		_ptsGetTableBlock()._switchDescRow({ state: jQuery(this).prop('checked') });	//"!" here is because option is actually for hide
	});
	// End/dsbl Foot row check
	jQuery('.ptsTableSettingsShell input[name="params[enb_foot_row]"]').change(function(){
		_ptsGetTableBlock()._switchFootRow({ state: jQuery(this).prop('checked') });	//"!" here is because option is actually for hide
	});
	// End/dsbl responsive mode
	jQuery('.ptsTableSettingsShell input[name="params[enb_responsive]"]').change(function(){
		// As prameter is disable, but option - is enable, this was done for more user-friendly options names in admin area
		_ptsGetTableBlock().setParam('dsbl_responsive', jQuery(this).prop('checked') ? 0 : 1);
		_ptsSetResponsiveMinColWidth();
	});
	// Responsive columns width manipulation
	jQuery('.ptsTableSettingsShell input[name="params[resp_min_col_width]"]').change(function(){
		_ptsGetTableBlock().setParam('resp_min_col_width', jQuery('.ptsTableSettingsShell input[name="params[resp_min_col_width]"]').val());
	});
	_ptsSetResponsiveMinColWidth();
	jQuery('.ptsTableSettingsShell input[name="params[disable_custom_tooltip_style]"]').change(function(){
		_ptsGetTableBlock().setParam('disable_custom_tooltip_style', jQuery(this).prop('checked') ? 1 : 0);
	});
	//Enable / disable switch
	jQuery('.ptsTableSettingsShell input[name="params[enable_switch_toggle]"]').change(function(){
		_ptsGetTableBlock().setParam('enable_switch_toggle', jQuery(this).prop('checked') ? 1 : 0);
		//_ptsEnableEditButton();
	});
    /*
    _ptsEnableEditButton();
    //Set switch options
    jQuery('.ptsTableSettingsShell input[name="params[switch_options]"]').change(function(){
        _ptsGetTableBlock().setParam('switch_options', jQuery(this).val());
    });
*/
	//Set switch text
	jQuery('.ptsTableSettingsShell input[name="params[switch_text]"]').change(function(){
        _ptsGetTableBlock().setParam('switch_text', jQuery(this).val());
    });
    //Set switch type
    jQuery('.ptsTableSettingsShell select[name="params[switch_type]"]').change(function(){
        _ptsGetTableBlock().setParam('switch_type', jQuery(this).val());
    });
    //Set switch position
    jQuery('.ptsTableSettingsShell select[name="params[switch_position]"]').change(function(){
    	_ptsGetTableBlock().setParam('switch_position', jQuery(this).val());
    });
    //Set switch options names / selected
    jQuery('.ptsTableSettingsShell input[name="params[option_name_input]"]').change(function(){
        _ptsGetTableBlock().setParam('option_name_input', jQuery(this).val());
    });

    // Border color picker
    _ptsCreateColorPickerFrom('.ptsSwitchColorBorder', function(pcColor) {
        _ptsGetTableBlock().setParam('switch_color_border', pcColor.formatted);
    });
    // Button color picker
    _ptsCreateColorPickerFrom('.ptsSwitchColorButton', function(pcColor) {
        _ptsGetTableBlock().setParam('switch_color_button', pcColor.formatted);
    });
    // Button text color picker
    _ptsCreateColorPickerFrom('.ptsSwitchColorButtonText', function(pcColor) {
        _ptsGetTableBlock().setParam('switch_color_button_text', pcColor.formatted);
    });
    // Button text no active color picker
    _ptsCreateColorPickerFrom('.ptsSwitchColorButtonTextNoactive', function(pcColor) {
        _ptsGetTableBlock().setParam('switch_color_button_text_noactive', pcColor.formatted);
    });


	// always save 'is_horisontal_row_type'
	_ptsGetTableBlock().setParam('is_horisontal_row_type', jQuery('.ptsTableSettingsShell input[name="params[is_horisontal_row_type]"]').val());
	// Preview btn click
	jQuery('.ptsTablePreviewBtn').click(function(){
		jQuery('html, body').animate({
			scrollTop: jQuery("#ptsCanvas").offset().top - jQuery('#wpadminbar').height()
		}, 1000);
		return false;
	});
	// Check old table html, if required - rebuild current right now according to old changes.
	// This required for "Change Template" functionality
	_ptsCheckOldTemplateHtml();
	// Shortcodes example switch
	jQuery('#ptsTableShortcodeExampleSel').change(function(){
		jQuery('#ptsTableShortcodeShell, #ptsTablePhpCodeShell').hide();
		var showId = '';
		switch(jQuery(this).val()) {
			case 'shortcode':
				showId = 'ptsTableShortcodeShell';
				break;
			case 'php_code':
				showId = 'ptsTablePhpCodeShell';
				break;
		}
		jQuery('#'+ showId).show();
	}).trigger('change');
	// Transform al custom chosen selects
	jQuery('.chosen').chosen({
		disable_search_threshold: 5
	});
	_ptsTableInitSaveAsCopyDlg();

	jQuery('.ptsTableEditCssBtn').click(function(){
		ptsBlockCssEditor.show(ptsGetFabric().getBlocks()[0]);

		return false;
	});

	jQuery('#containerWrapper .ptsSettingsTabs a').on('click', function(){
		var tab = $(this)
		,	href = tab.attr('data-href');
		jQuery('#containerWrapper .ptsSettingsTabs a').removeClass('nav-tab-active');
		jQuery('#containerWrapper .ptsSettingsContent div').removeClass('active');

        tab.addClass('nav-tab-active');
		jQuery('#containerWrapper .ptsSettingsContent .' + href).addClass('active');
	});
	// jQuery(".ptsTableSettingsShell .tooltipstered").removeAttr("title");
});
function _ptsSetResponsiveMinColWidth() {
	var $ptsRespMinColWidthObj = jQuery(".ptsRespMinColW");
	if(jQuery('.ptsTableSettingsShell input[name="params[enb_responsive]"]').prop("checked")) {
		$ptsRespMinColWidthObj.removeClass("ptsDisplNone");
	} else {
		$ptsRespMinColWidthObj.addClass("ptsDisplNone");
	}
	_ptsGetTableBlock().setParam('resp_min_col_width', jQuery('.ptsTableSettingsShell input[name="params[resp_min_col_width]"]').val());

	//set no responsive mode for Horizontal table
	if(_ptsGetTableBlock().getParam('is_horisontal_row_type') == '1' &&
		!jQuery('.ptsTableSettingsShell input[name="params[enb_responsive]"]').prop("checked")) {
        var table = _ptsGetTableBlock()._$.find('.ptsCol').css('min-width', '800px');
    }else if(_ptsGetTableBlock().getParam('is_horisontal_row_type') == '1'
		&& jQuery('.ptsTableSettingsShell input[name="params[enb_responsive]"]').prop("checked")){
        var table = _ptsGetTableBlock()._$.find('.ptsCol').css('min-width', '100%');
	}
}
/*
function _ptsEnableEditButton() {
	var $ptsEditButton = jQuery(".ptsSwitchToggleOpt");
	var $toggleButton = jQuery(".ptsSwitchWrapper");

	if(jQuery('.ptsTableSettingsShell input[name="params[enable_switch_toggle]"]').prop("checked")) {
		$ptsEditButton.removeClass("ptsDisplNone");
		$toggleButton.removeClass("ptsDisplNone");
	} else {
		$ptsEditButton.addClass("ptsDisplNone");
		$toggleButton.addClass("ptsDisplNone");
	}

	_ptsGetTableBlock().setParam('resp_min_col_width', jQuery('.ptsTableSettingsShell input[name="params[resp_min_col_width]"]').val());
}
*/
function _ptsTableInitSaveAsCopyDlg() {
	var $container = jQuery('#ptsTableSaveAsCopyWnd').dialog({
		modal:    true
	,	autoOpen: false
	,	width: 460
	,	height: 180
	,	buttons:  {
			OK: function() {
				jQuery('#ptsTableSaveAsCopyForm').submit();
			}
		,	Cancel: function() {
				$container.dialog('close');
			}
		}
	});
	jQuery('#ptsTableSaveAsCopyForm').submit(function(){
		jQuery(this).sendFormPts({
			msgElID: 'ptsTableSaveAsCopyMsg'
		,	onSuccess: function(res) {
				if(!res.error && res.data.edit_link) {
					toeRedirect( res.data.edit_link );
				}
			}
		});
		return false;
	});
	jQuery('.ptsTableCloneBtn').click(function(){
		$container.dialog('open');
		return false;
	});
}
function _ptsCheckBgColorNotice() {
	var noticeAboutBgColorShown = parseInt(getCookiePts('pts_bg_color_notice_shown'));
	if(!noticeAboutBgColorShown) {
		var $cols = _ptsGetTableBlock()._getCols( parseInt(_ptsGetTableBlock().getParam('enb_desc_col')) )
		,	haveColsWithoutFillColor = false;
		if($cols && $cols.size()) {
			$cols.each(function(){
				var colEl = _ptsGetTableBlock().getElementByIterNum( jQuery(this).data('iter-num') );
				if(colEl) {
					var enbFillColor = parseInt(colEl.get('enb-color'));
					if(!enbFillColor) {
						haveColsWithoutFillColor = true;
						return false;
					}
				}
			});
		}
		if(!haveColsWithoutFillColor) {	//So, each column have enabled bg color - tell user about this
			jQuery('#ptsTableAllColsHaveBgColorWnd').dialog({
				modal:    true
			,	width: 460
			,	buttons: [
					{
						text: 'OK, got it!',
						"class": 'ui-button ui-state-default',
						click: function() {                     
						   jQuery('#ptsTableAllColsHaveBgColorWnd').dialog('close');
						}
					}
				]
			,	close: function() {
					// Show all this notice - only once
					setCookiePts('pts_bg_color_notice_shown', 1, 365);	// Set cookie for one year - why not?:)
				}
			});
		}
	}
}
function ptsFinishEditTableLabel(label) {
	if(jQuery('#ptsTableEditableLabelShell').data('sending')) return;
	if(!jQuery('#ptsTableEditableLabelTxt').data('ready')) return;
	jQuery('#ptsTableEditableLabelShell').data('sending', 1);
	jQuery.sendFormPts({
		btn: jQuery('#ptsTableEditableLabelShell')
	,	data: {mod: 'tables', action: 'updateLabel', label: label, id: _ptsGetTableBlock().get('id')}
	,	onSuccess: function(res) {
			if(!res.error) {
				var $labelHtml = jQuery('#ptsTableEditableLabel')
				,	$labelTxt = jQuery('#ptsTableEditableLabelTxt');
				$labelHtml.html( jQuery.trim($labelTxt.val()) );
				$labelTxt.hide( g_ptsAnimationSpeed ).data('ready', 0);
				$labelHtml.show( g_ptsAnimationSpeed );
				jQuery('#ptsTableEditableLabelShell').data('edit-on', 0);
			}
			jQuery('#ptsTableEditableLabelShell').data('sending', 0);
		}
	});
}
function _ptsSetColsNumSetting(block) {
	var colsNum = block.getColsNum();
	jQuery('.ptsTableColsNum_'+ block.get('view_id')).html( colsNum );
	block.setParam('cols_num', colsNum);
}
function _ptsSetRowsNumSetting(block) {
	var rowsNum = block.getRowsNum();
	jQuery('.ptsTableRowsNum_'+ block.get('view_id')).html( rowsNum );
	block.setParam('rows_num', rowsNum);
}
function _ptsGetTableBlock() {
	return g_ptsTableBlock;
}
function _ptsCreateColorPickerFrom(selector, callbackFunc) {
	var $input = jQuery(selector);
	var currColorPickerOpt = jQuery.extend(g_ptsVandColorPickerOptions, {
		'altField': selector + 'Tear',
		'select': function(event, cpColor) {
			if(callbackFunc && typeof callbackFunc === "function") {
				callbackFunc(cpColor);
			}
		},
		'position': {'my': 'center top', 'at': 'right bottom', 'of': selector + 'Tear'},
	});
	$input.colorpicker(currColorPickerOpt);
}
function _ptsCheckOldTemplateHtml() {
	var oldHtml = _ptsGetTableBlock().getParam('old_html');
	if(oldHtml && oldHtml != '') {
		var table = _ptsGetTableBlock()
		,	$tmpDiv = jQuery('<div style="display: none;" />').appendTo('body').html( oldHtml )
		,	$oldCols = $tmpDiv.find('.ptsCol:not(.ptsTableDescCol)')
		,	oldColsNum = $oldCols.size()
		,	$cols = table._getCols()
		,	colsNum = $cols.size()
		,	$oldFirstCol = $oldCols.first()
		,	oldRowsNum = $oldFirstCol.find('.ptsRows .ptsCell').size()
		,	rowsNum = table.getRowsNum();
		if(oldColsNum != colsNum) {
			var i = oldColsNum - colsNum
			,	currColNum = colsNum;
			while(i) {
				if(i > 0) {
					table.addColumn();
					i--;
				} else {
					table.removeCol( currColNum - 1 );
					currColNum--;
					i++;
				}
			}
		}
		if(oldRowsNum != rowsNum) {
			var i = oldRowsNum - rowsNum
			,	currRowNum = rowsNum;
			while(i) {
				if(i > 0) {
					table.addRow();
					i--;
				} else {
					table.removeRow( currRowNum - 1 );
					currRowNum--;
					i++;
				}
			}
		}
		var $oldColsWithDesc = $tmpDiv.find('.ptsCol')
		,	$newColsWithDesc = table._getCols( true )
		,	colSelectors = table.getColSelectors();
		$newColsWithDesc.each(function(index){
			for(var key in colSelectors) {
				if(key == 'rows') continue;	// Don't replace all rows, let's replace each cell - step-by-step
				var $newItems = jQuery(this).find( colSelectors[key].sel );
				$newItems.each(function(itemIndex){
					var $newItem = jQuery(this);
					$newItem.find('.ptsEl').each(function(){	// Remove all old elements
						table.removeElementByIterNum( jQuery(this).data('iter-num') );
					});
					$newItem.html( $oldColsWithDesc.filter(':eq('+ index+ ')').find( colSelectors[key].sel ).filter(':eq('+ itemIndex+ ')').html() );
					table._initElementsForArea( $newItem );
				});
			}
		});
		$tmpDiv.remove();	// Goodbay old data:)
		setTimeout(function(){	// Re-save new data
			table.contentChanged();
			table._initCellsEdit();	// Let it be here
			table._switchHeadRow();
			table._switchDescRow();
			table._switchFootRow();
			table._initCellsMovable();
			_ptsGetTableBlock().setParam('old_html');
			_ptsSaveCanvas({
				sendData: {remove_old_html: 1}
			});
		}, g_ptsAnimationSpeed);
	}
}

jQuery(window).on('toggleChangeCellHeight', function () {
	var table = _ptsGetTableBlock();
	table.contentChanged();
});
