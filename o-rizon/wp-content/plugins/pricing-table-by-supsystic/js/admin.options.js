var ptsAdminFormChanged = [];
window.onbeforeunload = function(){
	// If there are at lease one unsaved form - show message for confirnation for page leave
	if(ptsAdminFormChanged.length)
		return 'Some changes were not-saved. Are you sure you want to leave?';
};
jQuery(document).ready(function(){
	ptsInitMainPromoPopup();
	if(typeof(ptsActiveTab) != 'undefined' && ptsActiveTab != 'main_page' && jQuery('#toplevel_page_tables-supsystic').hasClass('wp-has-current-submenu')) {
		var subMenus = jQuery('#toplevel_page_tables-supsystic').find('.wp-submenu li');
		subMenus.removeClass('current').each(function(){
			if(jQuery(this).find('a[href$="&tab='+ ptsActiveTab+ '"]').size()) {
				jQuery(this).addClass('current');
			}
		});
	}
	
	// Timeout - is to count only user changes, because some changes can be done auto when form is loaded
	setTimeout(function() {
		// If some changes was made in those forms and they were not saved - show message for confirnation before page reload
		var formsPreventLeave = [];
		if(formsPreventLeave && formsPreventLeave.length) {
			jQuery('#'+ formsPreventLeave.join(', #')).find('input,select').change(function(){
				var formId = jQuery(this).parents('form:first').attr('id');
				changeAdminFormPts(formId);
			});
			jQuery('#'+ formsPreventLeave.join(', #')).find('input[type=text],textarea').keyup(function(){
				var formId = jQuery(this).parents('form:first').attr('id');
				changeAdminFormPts(formId);
			});
			jQuery('#'+ formsPreventLeave.join(', #')).submit(function(){
				adminFormSavedPts( jQuery(this).attr('id') );
			});
		}
	}, 1000);

	if(jQuery('.ptsInputsWithDescrForm').size()) {
		jQuery('.ptsInputsWithDescrForm').find('input[type=checkbox][data-optkey]').change(function(){
			var optKey = jQuery(this).data('optkey')
			,	descShell = jQuery('#ptsFormOptDetails_'+ optKey);
			if(descShell.size()) {
				if(jQuery(this).attr('checked')) {
					descShell.slideDown( 300 );
				} else {
					descShell.slideUp( 300 );
				}
			}
		}).trigger('change');
	}
	ptsInitStickyItem();
	ptsInitCustomCheckRadio();
	//ptsInitCustomSelect();
	
	jQuery('.ptsFieldsetToggled').each(function(){
		var self = this;
		jQuery(self).find('.ptsFieldsetContent').hide();
		jQuery(self).find('.ptsFieldsetToggleBtn').click(function(){
			var icon = jQuery(this).find('i')
			,	show = icon.hasClass('fa-plus');
			show ? icon.removeClass('fa-plus').addClass('fa-minus') : icon.removeClass('fa-minus').addClass('fa-plus');
			jQuery(self).find('.ptsFieldsetContent').slideToggle( 300, function(){
				if(show) {
					jQuery(this).find('textarea').each(function(i, el){
						if(typeof(this.CodeMirrorEditor) !== 'undefined') {
							this.CodeMirrorEditor.refresh();
						}
					});
				}
			} );
			return false;
		});
	});
	// Go to Top button init
	if(jQuery('#ptsPopupGoToTopBtn').size()) {
		jQuery('#ptsPopupGoToTopBtn').click(function(){
			jQuery('html, body').animate({
				scrollTop: 0
			}, 1000);
			jQuery(this).parents('#ptsPopupGoToTop:first').hide();
			return false;
		});
	}
	ptsInitTooltips();
	if(jQuery('.ptsCopyTextCode').size()) {
		var cloneWidthElement =  jQuery('<span class="sup-shortcode" />').appendTo('.supsystic-plugin');
		jQuery('.ptsCopyTextCode').attr('readonly', 'readonly').click(function(){
			this.setSelectionRange(0, this.value.length);
		}).focus(function(){
			this.setSelectionRange(0, this.value.length);
		});
		jQuery('input.ptsCopyTextCode').each(function(){
			cloneWidthElement.html( str_replace(jQuery(this).val(), '<', 'P') );
			jQuery(this).width( cloneWidthElement.width() );
		});
		cloneWidthElement.remove();
	}
	// Check for showing review notice after a week usage
    ptsInitPlugNotices();
});
function ptsInitTooltips( selector ) {
	var tooltipsterSettings = {
		contentAsHTML: true
		,	interactive: true
		,	speed: 250
		,	delay: 0
		,	animation: 'swing'
		,	maxWidth: 450
	}
		,	findPos = {
			'.supsystic-tooltip': 'top-left'
		,	'.supsystic-tooltip-bottom': 'bottom-left'
		,	'.supsystic-tooltip-left': 'left'
		,	'.supsystic-tooltip-right': 'right'
	}
		,	$findIn = selector ? jQuery( selector ) : false;
	for(var k in findPos) {
		if(typeof(k) === 'string') {
			var $tips = $findIn ? $findIn.find( k ) : jQuery( k ).not('.sup-no-init');
			if($tips && $tips.size()) {
				tooltipsterSettings.position = findPos[ k ];
				// Fallback for case if library was not loaded
				if(!$tips.tooltipster) continue;
				$tips.tooltipster( tooltipsterSettings );
				$tips.removeAttr("title");
				//jQuery(".ptsTableSettingsShell .tooltipstered").removeAttr("title");
			}
		}
	}
}

function changeAdminFormPts(formId) {
	if(jQuery.inArray(formId, ptsAdminFormChanged) == -1)
		ptsAdminFormChanged.push(formId);
}
function adminFormSavedPts(formId) {
	if(ptsAdminFormChanged.length) {
		for(var i in ptsAdminFormChanged) {
			if(ptsAdminFormChanged[i] == formId) {
				ptsAdminFormChanged.pop(i);
			}
		}
	}
}
function checkAdminFormSaved() {
	if(ptsAdminFormChanged.length) {
		if(!confirm(toeLangPts('Some changes were not-saved. Are you sure you want to leave?'))) {
			return false;
		}
		ptsAdminFormChanged = [];	// Clear unsaved forms array - if user wanted to do this
	}
	return true;
}
function isAdminFormChanged(formId) {
	if(ptsAdminFormChanged.length) {
		for(var i in ptsAdminFormChanged) {
			if(ptsAdminFormChanged[i] == formId) {
				return true;
			}
		}
	}
	return false;
}
/*Some items should be always on users screen*/
function ptsInitStickyItem() {
	jQuery(window).scroll(function(){
		var stickiItemsSelectors = [/*'.ui-jqgrid-hdiv', */'.supsystic-sticky']
		,	elementsUsePaddingNext = [/*'.ui-jqgrid-hdiv', */'.supsystic-bar']	// For example - if we stick row - then all other should not offest to top after we will place element as fixed
		,	wpTollbarHeight = 32
		,	wndScrollTop = jQuery(window).scrollTop() + wpTollbarHeight
		,	footer = jQuery('.ptsAdminFooterShell')
		,	footerHeight = footer && footer.size() ? footer.height() : 0
		,	docHeight = jQuery(document).height()
		,	wasSticking = false
		,	wasUnSticking = false;
		/*if(jQuery('#wpbody-content .update-nag').size()) {	// Not used for now
			wpTollbarHeight += parseInt(jQuery('#wpbody-content .update-nag').outerHeight());
		}*/
		for(var i = 0; i < stickiItemsSelectors.length; i++) {
			jQuery(stickiItemsSelectors[ i ]).each(function(){
				var element = jQuery(this);
				if(element && element.size() && !element.hasClass('sticky-ignore')) {
					var scrollMinPos = element.offset().top
					,	prevScrollMinPos = parseInt(element.data('scrollMinPos'))
					,	useNextElementPadding = toeInArray(stickiItemsSelectors[ i ], elementsUsePaddingNext) !== -1 || element.hasClass('sticky-padd-next')
					,	currentScrollTop = wndScrollTop
					,	calcPrevHeight = element.data('prev-height')
					,	currentBorderHeight = wpTollbarHeight
					,	usePrevHeight = 0;
					if(calcPrevHeight) {
						usePrevHeight = jQuery(calcPrevHeight).outerHeight();
						currentBorderHeight += usePrevHeight;
					}
					if(currentScrollTop > scrollMinPos && !element.hasClass('supsystic-sticky-active')) {	// Start sticking
						if(element.hasClass('sticky-save-width')) {
							element.width( element.width() );
						}
						element.addClass('supsystic-sticky-active').data('scrollMinPos', scrollMinPos).css({
							'top': currentBorderHeight
						});
						if(useNextElementPadding) {
							//element.addClass('supsystic-sticky-active-bordered');
							var nextElement = element.next();
							if(nextElement && nextElement.size()) {
								nextElement.data('prevPaddingTop', nextElement.css('padding-top'));
								var addToNextPadding = parseInt(element.data('next-padding-add'));
								addToNextPadding = addToNextPadding ? addToNextPadding : 0;
								nextElement.css({
									'padding-top': element.outerHeight() + usePrevHeight  + addToNextPadding
								});
							}
						}
						wasSticking = true;
						element.trigger('startSticky');
					} else if(!isNaN(prevScrollMinPos) && currentScrollTop <= prevScrollMinPos) {	// Stop sticking
						element.removeClass('supsystic-sticky-active').data('scrollMinPos', 0).css({
							//'top': 0
						});
						if(element.hasClass('sticky-save-width')) {
							if(element.hasClass('sticky-base-width-auto')) {
								element.css('width', 'auto');
							}
							//element.removeClass('sticky-full-width');
						}
						if(useNextElementPadding) {
							//element.removeClass('supsystic-sticky-active-bordered');
							var nextElement = element.next();
							if(nextElement && nextElement.size()) {
								var nextPrevPaddingTop = parseInt(nextElement.data('prevPaddingTop'));
								if(isNaN(nextPrevPaddingTop))
									nextPrevPaddingTop = 0;
								nextElement.css({
									'padding-top': nextPrevPaddingTop
								});
							}
						}
						element.trigger('stopSticky');
						wasUnSticking = true;
					} else {	// Check new stick position
						if(element.hasClass('supsystic-sticky-active')) {
							if(footerHeight) {
								var elementHeight = element.height()
								,	heightCorrection = 32
								,	topDiff = docHeight - footerHeight - (currentScrollTop + elementHeight + heightCorrection);
								if(topDiff < 0) {
									element.css({
										'top': currentBorderHeight + topDiff
									});
								} else {
									element.css({
										'top': currentBorderHeight
									});
								}
							}
							// If at least on element is still sticking - count it as all is working
							wasSticking = wasUnSticking = false;
						}
					}
				}
			});
		}
		if(wasSticking) {
			if(jQuery('#ptsPopupGoToTop').size())
				jQuery('#ptsPopupGoToTop').show();
		} else if(wasUnSticking) {
			if(jQuery('#ptsPopupGoToTop').size())
				jQuery('#ptsPopupGoToTop').hide();
		}
	});
}
function ptsGetTxtEditorVal(id) {
	if(typeof(tinyMCE) !== 'undefined' && tinyMCE.get( id ) && !jQuery('#'+ id).is(':visible'))
		return tinyMCE.get( id ).getContent();
	else
		return jQuery('#'+ id).val();
}
function ptsSetTxtEditorVal(id, content) {
	if(typeof(tinyMCE) !== 'undefined' && tinyMCE && tinyMCE.get( id ) && !jQuery('#'+ id).is(':visible'))
		tinyMCE.get( id ).setContent(content);
	else
		jQuery('#'+ id).val( content );
}
/**
 * Add data to jqGrid object post params search
 * @param {object} param Search params to set
 * @param {string} gridSelectorId ID of grid table html element
 */
function ptsGridSetListSearch(param, gridSelectorId) {
	jQuery('#'+ gridSelectorId).setGridParam({
		postData: {
			search: param
		}
	});
}
/**
 * Set data to jqGrid object post params search and trigger search
 * @param {object} param Search params to set
 * @param {string} gridSelectorId ID of grid table html element
 */
function ptsGridDoListSearch(param, gridSelectorId) {
	ptsGridSetListSearch(param, gridSelectorId);
	jQuery('#'+ gridSelectorId).trigger( 'reloadGrid' );
}
/**
 * Get row data from jqGrid
 * @param {number} id Item ID (from database for example)
 * @param {string} gridSelectorId ID of grid table html element
 * @return {object} Row data
 */
function ptsGetGridDataById(id, gridSelectorId) {
	var rowId = getGridRowId(id, gridSelectorId);
	if(rowId) {
		return jQuery('#'+ gridSelectorId).jqGrid ('getRowData', rowId);
	}
	return false;
}
/**
 * Get cell data from jqGrid
 * @param {number} id Item ID (from database for example)
 * @param {string} column Column name
 * @param {string} gridSelectorId ID of grid table html element
 * @return {string} Cell data
 */
function ptsGetGridColDataById(id, column, gridSelectorId) {
	var rowId = getGridRowId(id, gridSelectorId);
	if(rowId) {
		return jQuery('#'+ gridSelectorId).jqGrid ('getCell', rowId, column);
	}
	return false;
}
/**
 * Get grid row ID (ID of table row) from item ID (from database ID for example)
 * @param {number} id Item ID (from database for example)
 * @param {string} gridSelectorId ID of grid table html element
 * @return {number} Table row ID
 */
function getGridRowId(id, gridSelectorId) {
	var rowId = parseInt(jQuery('#'+ gridSelectorId).find('[aria-describedby='+ gridSelectorId+ '_id][title='+ id+ ']').parent('tr:first').index());
	if(!rowId) {
		console.log('CAN NOT FIND ITEM WITH ID  '+ id);
		return false;
	}
	return rowId;
}
function prepareToPlotDate(data) {
	if(typeof(data) === 'string') {
		if(data) {
			
			data = str_replace(data, '/', '-');
			console.log(data, new Date(data));
			return (new Date(data)).getTime();
		}
	}
	return data;
}
function ptsInitPlugNotices() {
	var $notices = jQuery('.supsystic-admin-notice');
	if($notices && $notices.size()) {
		$notices.each(function(){
			jQuery(this).find('.notice-dismiss').click(function(){
				var $notice = jQuery(this).parents('.supsystic-admin-notice');
				if(!$notice.data('stats-sent')) {
					// User closed this message - that is his choise, let's respect this and save it's saved status
					jQuery.sendFormPts({
						data: {mod: 'supsystic_promo', action: 'addNoticeAction', code: $notice.data('code'), choice: 'hide'}
					});
				}
			});
			jQuery(this).find('[data-statistic-code]').click(function(){
				var href = jQuery(this).attr('href')
				,	$notice = jQuery(this).parents('.supsystic-admin-notice');
				jQuery.sendFormPts({
					data: {mod: 'supsystic_promo', action: 'addNoticeAction', code: $notice.data('code'), choice: jQuery(this).data('statistic-code')}
				});
				$notice.data('stats-sent', 1).find('.notice-dismiss').trigger('click');
				if(!href || href === '' || href === '#')
					return false;
			});
		});
	}
}
/**
 * Main promo popup will show each time user will try to modify PRO option with free version only
 */
function ptsInitMainPromoPopup() {
	if(!PTS_DATA.isPro) {
		var $proOptWnd = jQuery('#ptsOptInProWnd').dialog({
			modal:    true
		,	autoOpen: false
		,	width: 540
		,	height: 200
		});
		jQuery('.ptsProOpt').change(function(e){
			e.stopPropagation();
			var needShow = true
			,	isRadio = jQuery(this).attr('type') == 'radio'
			,	isCheck = jQuery(this).attr('type') == 'checkbox';
			if(isRadio && !jQuery(this).attr('checked')) {
				needShow = false;
			}
			if(!needShow) {
				return;
			}
			if(isRadio) {
				jQuery('input[name="'+ jQuery(this).attr('name')+ '"]:first').parents('label:first').click();
				if(jQuery(this).parents('.iradio_minimal:first').size()) {
					var self = this;
					setTimeout(function(){
						jQuery(self).parents('.iradio_minimal:first').removeClass('checked');
					}, 10);
				}
			}
			var parent = null;
			if(jQuery(this).parents('#ptsPopupMainOpts').size()) {
				parent = jQuery(this).parents('label:first');
			} else if(jQuery(this).parents('.ptsPopupOptRow:first').size()) {
				parent = jQuery(this).parents('.ptsPopupOptRow:first');
			} else {
				parent = jQuery(this).parents('tr:first');
			}
			if(!parent.size()) return;
			var promoLink = parent.find('.ptsProOptMiniLabel a').attr('href');
			if(promoLink && promoLink != '') {
				jQuery('#ptsOptInProWnd a').attr('href', promoLink);
			}
			$proOptWnd.dialog('open');
			return false;
		});
	}
}