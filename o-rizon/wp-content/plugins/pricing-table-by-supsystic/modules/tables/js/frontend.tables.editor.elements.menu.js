function ptsElementMenu(menuOriginalId, element, btnsClb, params) {
	params = params || {};
	this._$ = null;
	this._animationSpeed = g_ptsAnimationSpeed;
	this._menuOriginalId = menuOriginalId;
	this._element = element;
	this._btnsClb = btnsClb;
	this._visible = false;
	this._isMovable = false;
	this._changeable = params.changeable ? params.changeable : false;
	this._inAnimation = false;
	this._id = 'ptsElMenu_'+ mtRand(1, 99999);
	this._showEvent = params.showEvent ? params.showEvent : 'click';
	this.init();
}
ptsElementMenu.prototype.getId = function() {
	return this._id;
};
ptsElementMenu.prototype.setMovable = function(state) {
	this._isMovable = state;
};
ptsElementMenu.prototype.setChangeable = function(state) {
	this._changeable = state;
};
ptsElementMenu.prototype._afterAppendToElement = function() {
	if(this._changeable) {
		/** this._updateType(); **/
	}
};
ptsElementMenu.prototype._updateType = function(refreshCheck) {
	if(this._changeable) {
		var type = this._element.get('type');

		this._$
			.find('[name=type]').removeAttr('checked')
			.filter('[value='+ type + ']').attr('checked', 'checked');
	}
};
ptsElementMenu.prototype.$ = function() {
	return this._$;
};
ptsElementMenu.prototype.init = function() {
	var self = this
	,	$original = jQuery('#'+ this._menuOriginalId);
	if(!$original.data('icheck-cleared')) {
		$original.find('input').iCheck('destroy');
		$original.data('icheck-cleared', 1);
	}
	this._$ = $original
		.clone()
		.attr('id', this._id)
		.appendTo('body');
	this._afterAppendToElement();
	
	ptsInitCustomCheckRadio( this._$ );
	this._fixClickOnRadio();
	this.reposite();
	this._initAddHtmlAttributes();

	if(this._btnsClb) {
		for(var selector in this._btnsClb) {
			if(this._$.find( selector ).size()) {
				this._$.find( selector ).click(function(){
					self._btnsClb[ jQuery(this).data('click-clb-selector') ]();
					return false;
				}).data('click-clb-selector', selector);
			}
		}
	}
	
	this._initSubMenus();
};
ptsElementMenu.prototype._initAddHtmlAttributes = function() {
	var attrs = new ptsChangeElAttrs(this);
};
ptsElementMenu.prototype._fixClickOnRadio = function() {
	this._$.find('.ptsElMenuBtn').each(function(){
		if(jQuery(this).find('[type=radio]').size()) {
			jQuery(this).find('[type=radio]').click(function(){
				jQuery(this).parents('.ptsElMenuBtn:first').click();
			});
		}
	});
};
ptsElementMenu.prototype._hideSubMenus = function() {
	if(!this._$) return;	// If menu was already destroyed, with destroy element for example
	var menuAtBottom = this._$.hasClass('ptsElMenuBottom')
	,	menuOpenBottom = this._$.hasClass('ptsMenuOpenBottom')
	,	self = this;
	this._inAnimation = true;
	this._$.find('.ptsElMenuSubPanel[data-sub-panel]:visible').each(function(){
		jQuery(this).slideUp(self._animationSpeed);
	});
	this._$.removeClass('ptsMenuSubOpened');
	if(!menuAtBottom && !menuOpenBottom) {
		this._$.data('animation-in-process', 1).animate({
			'top': this._$.position().top
		}, this._animationSpeed, function(){
			if(self._$) {
				self._$.data('animation-in-process', 0);
				self._inAnimation = false;
				if(self._isMovable) {
					self._$.trigger('ptsElMenuReposite', [self, null, null, null, false]);
				}
			}
		});
	} else if(menuOpenBottom) {
		this._$.removeClass('ptsMenuOpenBottom');
	}
};
ptsElementMenu.prototype._initSubMenus = function() {
	var self = this;
	if(this._$.find('.ptsElMenuBtn[data-sub-panel-show]').size()) {
		this._$.find('.ptsElMenuBtn').click(function(){
			self._hideSubMenus();
		});
		this._$.find('.ptsElMenuBtn[data-sub-panel-show]').click(function(){
			var subPanelShow = jQuery(this).data('sub-panel-show')
			,	subPanel = self._$.find('.ptsElMenuSubPanel[data-sub-panel="'+ subPanelShow+ '"]')
			,	menuPos = self._$.position()
			,	menuAtBottom = self._$.hasClass('ptsElMenuBottom')
			,	menuTop = menuPos.top;

			if(!subPanel.is(':visible')) {
				self._inAnimation = true;
				subPanel.slideDown(self._animationSpeed, function(){
					if(!menuAtBottom) {
						var subPanelHeight = subPanel.outerHeight();
						// If menu is too hight to move top - don't do this
						if(menuTop - subPanelHeight < g_ptsTopBarH) {
							self._$.addClass('ptsMenuOpenBottom');
							self._inAnimation = false;
						} else {
							self._$.animate({
								'top': menuTop - subPanelHeight
							}, self._animationSpeed, function(){
								self._inAnimation = false;
							});
							if(self._isMovable) {
								self._$.trigger('ptsElMenuReposite', [self, null, null, true, true]);
							}
						}
					}
				});
                // set nofollow checked if this._element contains it
                if(self._element._$.find('a[rel="nofollow"]').length && self._$.find('input[name$="link_rel_nofollow"]').length) {
                    self._$.find('input[name$="link_rel_nofollow"]').iCheck('check');
                }
				self._$.addClass('ptsMenuSubOpened');
			}
			return false;
		});
	}
};
ptsElementMenu.prototype.reposite = function() {
	var elOffset = this._element.$().offset()
	,	elWidth = this._element.$().width()
	//,	elHeight = this._element.$().height()
	,	width = this._$.width()
	,	height = this._$.height()
	,	left = elOffset.left - (width - elWidth) / 2
	,	top = elOffset.top - height;
	if(this._element.$().hasClass('hover')) {
		top -= g_ptsHoverMargin;
	}
	if(left < 0)
		left = 0;
	this._$.css({
		'left': (left)+ 'px'
	,	'top': (top)+ 'px'
	});
	var elementOffset = this._element.$().offset();
	this._menuOnBottom = elementOffset.top <= g_ptsTopBarH || this._element.$().data('menu-to-bottom');
	if(this._menuOnBottom) {
		this._$.addClass('ptsElMenuBottom');
	}
	if(this._isMovable) {
		this._$.trigger('ptsElMenuReposite', [this, top, left]);
	}
};
ptsElementMenu.prototype.destroy = function() {
	if(this._$) {
		this._$.remove();
		this._$ = null;
	}
};
ptsElementMenu.prototype.getShowEvent = function() {
	return this._showEvent;
};
ptsElementMenu.prototype.show = function() {
	if(!this._$) return;	// If menu was already destroyed, with destroy element for example
	if(!this._visible && !_ptsSortInProgress()) {
		// Let's hide all other element menus in current block before show this one
		this.getElement().getBlock().hideElementsMenus( this._showEvent );
		this.reposite();
		// And now - show current menu
		this._$.addClass('active');
		this._visible = true;
	}
};
ptsElementMenu.prototype.inAnimation = function() {
	return this._inAnimation;
};
ptsElementMenu.prototype.hide = function() {
	if(!this._$) return;	// If menu was already destroyed, with destroy element for example
	if(this._visible) {
		this._hideSubMenus();
		this._$.removeClass('active');
		this._visible = false;
		if(this._isMovable) {
			this._$.trigger('ptsElMenuHide', this);
		}
	}
};
ptsElementMenu.prototype.getElement = function() {
	return this._element;
};
ptsElementMenu.prototype._initColorpicker = function(params) {
	params = params || {};
	var self = this
	,	color = params.color ? params.color : this._element.get('color')
	,	$cpTear = jQuery("#"+self._$.attr('id') + ' .ptsColorPickInputTear')
	,	$spanColorPick = jQuery("#"+self._$.attr('id') + ' .ptsInlineColorPicker')
	,	oneColorPickerOpt = jQuery.extend(g_ptsVandColorPickerOptions, {
			'inline': true
		,	'altField': $cpTear
		,	'color': color
		,	'select': function(event, cpColor) {
				self._element._setColor(cpColor.formatted);
			}
		});
	$spanColorPick.colorpicker(oneColorPickerOpt);
};
ptsElementMenu.prototype.isVisible = function() {
	return this._visible;
};
function ptsElementMenu_btn(menuOriginalId, element, btnsClb) {
	ptsElementMenu_btn.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElementMenu_btn, ptsElementMenu);
ptsElementMenu_btn.prototype._afterAppendToElement = function() {
	ptsElementMenu_btn.superclass._afterAppendToElement.apply(this, arguments);

	this.$().find('.ptsPostLinkDisabled')
		.removeClass('ptsPostLinkDisabled')
		.addClass('ptsPostLinkList');

	// Link settings
	var self = this
	,	$btnLink = this._element._getEditArea()
	,	$linkInp = this._$.find('[name=btn_item_link]')
	,	$titleInp = this._$.find('[name=btn_item_title]')
	,	$newWndInp = this._$.find('[name=btn_item_link_new_wnd]')
	,	$relNofollow = this._$.find('[name=btn_item_link_rel_nofollow]');

	$linkInp.val( $btnLink.attr('href') );

	$linkInp.change(function(){
		$btnLink.attr('href', jQuery(this).val());
		$(document.body).trigger('updateToggleHtml');
	});

	//after change toggle replace href value in sub menu
	jQuery(document.body).on('changeToggleReplaceHrefBtn', function(){
		$linkInp.val( $btnLink.attr('href') );
	});

	$titleInp.val( $btnLink.attr('title') );
	$titleInp.change(function(){
		$btnLink.attr('title', jQuery(this).val());
	});
	$btnLink.attr('target') == '_blank' ? $newWndInp.attr('checked', 'checked') : $newWndInp.removeAttr('checked');
	$newWndInp.change(function(){
		jQuery(this).attr('checked') ? $btnLink.attr('target', '_blank') : $btnLink.removeAttr('target');
	});
	$relNofollow.change(function(){
		jQuery(this).attr('checked') ? $btnLink.attr('rel', 'nofollow') : $btnLink.removeAttr('rel');
	});
	// Color settings
	this._initColorpicker({
		color: this._element.get('bgcolor')
	});
};
function ptsElementMenu_icon(menuOriginalId, element, btnsClb) {
	ptsElementMenu_icon.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElementMenu_icon, ptsElementMenu);
ptsElementMenu_icon.prototype._afterAppendToElement = function() {
	ptsElementMenu_icon.superclass._afterAppendToElement.apply(this, arguments);

	this.$().find('.ptsPostLinkDisabled')
		.removeClass('ptsPostLinkDisabled')
		.addClass('ptsPostLinkList');

	var self = this
	,	iconSizeID = ['fa-lg', 'fa-2x', 'fa-3x', 'fa-4x', 'fa-5x']
	,	iconSize = {
		'fa-lg': '1.33333333em'
	,	'fa-2x': '2em'
	,	'fa-3x': '3em'
	,	'fa-4x': '4em'
	,	'fa-5x': '5em'
	}
	,	$icon = this._element._$.find('.fa');

	if ($icon.size()) {
		var	iconClasses = $icon.attr("class").split(' ').reverse()
		,	currentIconSize = undefined;
		
		for (var i in iconClasses) {
			if (iconSizeID.indexOf(iconClasses[i]) != -1) {
				currentIconSize = iconClasses[i];
				break;
			}
		}

		if (currentIconSize)
			this._$.find('[data-size="' + currentIconSize + '"]').addClass('active');
	}

	this._$.on('click', '[data-size]', function () {
		var classSize = jQuery(this).attr('data-size')
		,	$icon = self._element._$.find('.fa');

		if (! $icon.size() || ! classSize) return;

		$icon.removeClass(iconSizeID.join(' '));
		$icon.addClass(classSize);
		$icon.css('font-size', iconSize[classSize]);
		self._$.find('[data-size].active').removeClass('active');
		self._$.find('[data-size="' + classSize + '"]').addClass('active');
		self._element._block._refreshCellsHeight();
	});

	var btnLink = this._element._getLink()
	,	linkInp = this._$.find('[name=icon_item_link]')
	,	titleInp = this._$.find('[name=icon_item_title]')
	,	newWndInp = this._$.find('[name=icon_item_link_new_wnd]')
	,	relNofollow = this._$.find('[name=icon_item_link_rel_nofollow]');

	if(btnLink) {
		linkInp.val( btnLink.attr('href') );
		titleInp.val( btnLink.attr('title') );
		btnLink.attr('target') == '_blank' ? newWndInp.attr('checked', 'checked') : newWndInp.removeAttr('checked');
		btnLink.click(function(e){
			e.preventDefault();
		});
	}
	relNofollow.change(function () {
		self._element._isRelNofollow(jQuery(this).prop('checked') ? true: false);
	});
	linkInp.change(function(){
		self._element._setLinkAttr('href', jQuery(this).val());
	});
	titleInp.change(function(){
		self._element._setLinkAttr('title', jQuery(this).val());
	});
	newWndInp.change(function(){
		self._element._setLinkAttr('target', jQuery(this).prop('checked') ? true : false);
	});
	// Open links library
	this._$.find('.ptsIconLibBtn').click(function(){
		ptsUtils.showIconsLibWnd( self._element );
		return false;
	});
	// Color settings
	this._initColorpicker();
};
function ptsElementMenu_img(menuOriginalId, element, btnsClb) {
	ptsElementMenu_img.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElementMenu_img, ptsElementMenu);
ptsElementMenu_img.prototype._afterAppendToElement = function() {
	ptsElementMenu_img.superclass._afterAppendToElement.apply(this, arguments);

	this.$().find('.ptsPostLinkDisabled')
		.removeClass('ptsPostLinkDisabled')
		.addClass('ptsPostLinkList');

	this.getElement().get('type') === 'video'
		? this.$().find('[name=type][value=video]').attr('checked', 'checked')
		: this.$().find('[name=type][value=img]').attr('checked', 'checked');

	var self = this;
	var btnLink = this._element._getLink()
	,	linkInp = this._$.find('[name=image_item_link]')
	,	titleInp = this._$.find('[name=image_item_title]')
	,	newWndInp = this._$.find('[name=image_item_link_new_wnd]')
	,	relNofollow = this._$.find('[name=image_item_link_rel_nofollow]');

	if(btnLink) {
		linkInp.val( btnLink.attr('href') );
		titleInp.val( btnLink.attr('title') );
		btnLink.attr('target') == '_blank' ? newWndInp.attr('checked', 'checked') : newWndInp.removeAttr('checked');
		btnLink.click(function(e){
			e.preventDefault();
		});
	}

	relNofollow.change(function () {
		self._element._isRelNofollow(jQuery(this).prop('checked') ? true: false);
	});

	linkInp.change(function(){
		self._element._setLinkAttr('href', jQuery(this).val());
	});

	titleInp.change(function(){
		self._element._setLinkAttr('title', jQuery(this).val());
	});

	newWndInp.change(function(){
		self._element._setLinkAttr('target', jQuery(this).prop('checked') ? true : false);
	});
};
function ptsElementMenu_table_cell(menuOriginalId, element, btnsClb) {
	ptsElementMenu_table_cell.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElementMenu_table_cell, ptsElementMenu);
ptsElementMenu_table_cell.prototype._afterAppendToElement = function() {
	ptsElementMenu_table_cell.superclass._afterAppendToElement.apply(this, arguments);
	var type = this.getElement().get('type');
	if(!type)
		type = 'txt';
	this._$.find('[name=type][value='+ type+ ']').attr('checked', 'checked');
};
/**
 * Table col menu
 */
function ptsElementMenu_table_col(menuOriginalId, element, btnsClb) {
	ptsElementMenu_table_col.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElementMenu_table_col, ptsElementMenu);
ptsElementMenu_table_col.prototype._afterAppendToElement = function() {
	ptsElementMenu_table_col.superclass._afterAppendToElement.apply(this, arguments);
	var self = this;
	// Enb/Dslb fill color
	var $enbFillColorCheck = this._$.find('[name=enb_fill_color]');
	$enbFillColorCheck.change(function(){
		self.getElement().set('enb-color', jQuery(this).prop('checked') ? 1 : 0);
		self.getElement()._setColor();	// Just update it from existing color
		return false;
	});
	parseInt(this.getElement().get('enb-color'))
		? $enbFillColorCheck.attr('checked', 'checked')
		: $enbFillColorCheck.removeAttr('checked');
	// Color settings
	this._initColorpicker();
	// Enb/Dslb badge
	var $enbBadgeCheck = this._$.find('[name=enb_badge_col]');
	$enbBadgeCheck.change(function(){
		//self.getElement().set('enb-badge', jQuery(this).attr('checked') ? 1 : 0);
		if(jQuery(this).attr('checked')) {
			self.getElement()._setBadge();	// Just update it from existing badge data
			self.getElement()._showSelectBadgeWnd();
		} else {
			self.getElement()._disableBadge();
		}
		return false;
	});
	parseInt(this.getElement().get('enb-badge'))
		? $enbBadgeCheck.attr('checked', 'checked')
		: $enbBadgeCheck.removeAttr('checked');
	// Badge click
	this._btnsClb['.ptsColBadgeBtn'] = function() {
		self.getElement()._showSelectBadgeWnd();
	};
	if(PTS_DATA.isPro) {
		if(typeof(ptsMenuColSchedule) !== 'undefined') {
			this._scheduler = new ptsMenuColSchedule(this.$(), this.getElement());
		}
	} else {
		this._btnsClb['.ptsScheduleColBtn'] = function() {
			// TODO: Show here some PRO promo
		};
	}
	
};
function ptsElementMenu_table_cell_icon(menuOriginalId, element, btnsClb) {
	ptsElementMenu_table_cell_icon.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElementMenu_table_cell_icon, ptsElementMenu_icon);