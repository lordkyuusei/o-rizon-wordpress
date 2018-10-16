/**
 * Destroy current element
 */
ptsElementBase.prototype.destroy = function(clb) {
	if(this._$) {
		var childElements = this._getChildElements();
		if(childElements) {
			for(var i = 0; i < childElements.length; i++) {
				childElements[ i ]._remove();
			}
		}
		var self = this;
		this._$.slideUp(this._animationSpeed, function(){
			self._remove();
			if(clb && typeof(clb) === 'function') {
				clb();
			}
			_ptsSaveCanvas();
		});
	}
};
ptsElementBase.prototype.getMenuShowEvent = function() {
	return this._showMenuEvent;
};
ptsElementBase.prototype._remove = function() {
	if(this._showMenuEvent === 'click') {
		jQuery(document).unbind('click.menu_el_click_hide_'+ this.getId());
	}
	this._destroyMenu();
	if(this._$) {
		this._$.remove();
		this._$ = null;
	}
	this._afterDestroy();
	this._block.removeElementByIterNum( this.getIterNum() );
};
ptsElementBase.prototype._getChildElements = function() {
	var allFoundHtml = this._$.find('.ptsEl');
	if(allFoundHtml && allFoundHtml.size()) {
		var foundElements = []
		,	selfBlock = this.getBlock();
		allFoundHtml.each(function(){
			var element = selfBlock.getElementByIterNum( jQuery(this).data('iter-num') );
			if(element) {
				foundElements.push( element );
			}
		});
		return foundElements.length ? foundElements : false;
	}
	return false;
};
ptsElementBase.prototype._afterDestroy = function() {
	
};
ptsElementBase.prototype.beforeSave = function() {
	this._destroyMoveHandler();
};
ptsElementBase.prototype.afterSave = function() {
	this._initMoveHandler();
};
ptsElementBase.prototype._initMenu = function() {
	if(this._menuOriginalId && this._menuOriginalId != '') {
		this._initMenuClbs();
		var menuParams = {
			changeable: this._changeable
		,	showEvent: this._showMenuEvent
		};
		this._menu = new window[ this._menuClass ]( this._menuOriginalId, this, this._menuClbs, menuParams );
		
		if(!this._initedComplete) {
			var self = this;
			switch(this._showMenuEvent) {
				case 'hover':
					this._$.hover(function(){
						clearTimeout(jQuery(this).data('hide-menu-timeout'));
						self.showMenu();
					}, function(){
						jQuery(this).data('hide-menu-timeout', setTimeout(function(){
							self.hideMenu();
						}, 1000));	// Let it be visible 1 second more
					});
					this._menu.$().hover(function(){
						clearTimeout(jQuery(self._$).data('hide-menu-timeout'));
					}, function(){
						jQuery(self._$).data('hide-menu-timeout', setTimeout(function(){
							self.hideMenu();
						}, 1000));	// Let it be visible 1 second more
					});
					break;
				case 'click': default:
					this._$.click(function(e){
						e.stopPropagation();
						self.showMenu();
					});
					jQuery(document).bind('click.menu_el_click_hide_'+ this.getId(), jQuery.proxy(this._closeMenuOnDocClick, this));
					break;
			}
		}

		if(this._isMovable) {
			this._initMoveHandler();
			this._initMovableMenu();
		}
	
		this.initPostLinks(this._menu._$);
	}
};
ptsElementBase.prototype.initPostLinks = function($menu) {
	if (! this.includePostLinks) return;

	var $linkTab = $menu.find('.ptsPostLinkList')
	,	$field = null
	,	fieldSelector = $linkTab.attr('data-postlink-to');

	if (! fieldSelector.length) return;

	if (fieldSelector.indexOf(':parent') == 0) {
		fieldSelector = fieldSelector.substring(7, fieldSelector.length).trim();

		$field = $linkTab.parent().find(fieldSelector);
	} else {
		$field = jQuery(fieldSelector);
	}

	if (! $field.size()) return;

	this.showPostsLinks($linkTab);

	$linkTab.css({
		height: 120
	});

	$linkTab.on('click', 'li', function () {
		var $item = jQuery(this)
		,	url = $item.attr('data-value');

		if (! url) return;

		$field.val(url);

		$field.change();
	});

	$linkTab.slimScroll({
		height: 120
	,	railVisible: true
	,	alwaysVisible: true
	,	allowPageScroll: true
	,	color: '#f72497'
	,	opacity: 1
	,	distance: 0
	,	borderRadius: '3px'
	});

	$linkTab.parent('.slimScrollDiv')
		.addClass('ptsPostLinkRoot')
		.hide();

	var $rootTab = $linkTab.parent('.ptsPostLinkRoot');

	/** Hide and show handlers **/
	var ignoreHide = false
	,	isFocus = false;

	$field.on('postlink.hide', function () {
		$rootTab.hide();

		$linkTab.hide();

		$field.trigger('postlink.hide:after');
	});

	$field.focus(function () {
		$field.trigger('postlink.show');

		$rootTab.show();

		$linkTab.show();

		isFocus = true;

		$field.trigger('postlink.show:after');
	});

	$rootTab.hover(function () {
		ignoreHide = true;
	}, function () {
		ignoreHide = false;

		if (! isFocus) {
			$field.trigger('postlink.hide');
		}
	});

	$field.blur(function () {
		isFocus = false;

		if (!ignoreHide) {
			$field.trigger('postlink.hide');
		}
	});
};
ptsElementBase.prototype.escapeString = function  (str) {
	return jQuery('<div/>').text(str).html();
}
ptsElementBase.prototype.showPostsLinks = function($tab) {
	if (! $tab.find('ul').size()) {
		$tab.html('<ul></ul>');
	}

	$tab.find('ul').html('');

	for (var i in ptsEditor.posts) {
		$tab.find('ul')
			.append(
				'<li data-value="' + this.escapeString(ptsEditor.posts[i].url) + '">' +
					'<span>' + this.escapeString(ptsEditor.posts[i].title) + '</span>' +
				'</li>'
			);
	}
};
ptsElementBase.prototype._closeMenuOnDocClick = function(e, element) {
	if(!this._menu.isVisible()) return;
	var $target = jQuery(e.target);
	if(!this.$().find( $target ).size() && !this.getMenu().$().find($target).size()) {
		this.hideMenu();
	}
};
ptsElementBase.prototype.getMenu = function() {
	return this._menu;
};
ptsElementBase.prototype._initMovableMenu = function() {
	this._menu.setMovable(true);
	this._menu.$().bind('ptsElMenuReposite', function(e, menu, top, left, useAnimation, setActive){
		var element = menu.getElement()
		,	$element = element.$()
		,	$menu = menu.$()
		,	elWidth = $element.width()
		,	menuWidth = $menu.width()
		,	menuHeight = $menu.height();
		// var placePos = menu.$().find('.ptsElMenuMoveHandlerPlace').position()
		// ,	moveTop = -1 * menuHeight + placePos.top;
		// if($element.hasClass('hover')) {
		// 	moveTop -= g_ptsHoverMargin;
		// }

		// var elementParams = {
		// 	'top': moveTop
		// ,	'left': ((elWidth - menuWidth) / 2) + placePos.left - 10
		// };
		var elementParams = {
			'top': '0'
			,	'left': '-20px'
			//,	'background-color': '#f1f1f1'
		};

		if(typeof useAnimation != 'undefined' && useAnimation == true) {
			element._moveHandler.animate(elementParams, menu._animationSpeed);
		} else {
			element._moveHandler.css(elementParams);
		}
		if(typeof setActive == 'undefined' || setActive == true) {
			element._moveHandler.addClass('active')
		}
	}).bind('ptsElMenuHide', function(e, menu){
		var element = menu.getElement();
		if(!element._sortInProgress) {
			element._moveHandler.removeClass('active');
		}
	});
};
ptsElementBase.prototype.onSortStart = function() {
	this._sortInProgress = true;
	this._moveHandler.addClass('sortInProgress');
	this._menu.hide();
};
ptsElementBase.prototype.onSortStop = function() {
	this._sortInProgress = false;
	this._moveHandler.removeClass('sortInProgress');
	this._menu.show();
};
ptsElementBase.prototype._initMenuClbs = function() {
	var self = this;
	this._menuClbs['.ptsRemoveElBtn'] = function() {
		self.destroy();
	};
	if(this._changeable) {
		this._menuClbs['.ptsTypeTxtBtn'] = function() {
			self.getBlock().replaceElement(self, 'txt_item_html', 'txt');
		};
		this._menuClbs['.ptsTypeImgBtn'] = function() {
			self.getBlock().replaceElement(self, 'img_item_html', 'img');
		};
		this._menuClbs['.ptsTypeIconBtn'] = function() {
			self.getBlock().replaceElement(self, 'icon_item_html', 'icon');
		};
		this._menuClbs['.ptsTypeButtonBtn'] = function() {
			self.getBlock().replaceElement(self, 'icon_item_html', 'btn');
		};
	}
};
ptsElementBase.prototype._initMoveHandler = function() {
	if(this._isMovable && !this._moveHandler) {
		this._moveHandler = jQuery('#ptsMoveHandlerExl').clone().removeAttr('id').appendTo( this._$ );
	}
};
ptsElementBase.prototype._destroyMoveHandler = function() {
	if(this._isMovable) {
		this._moveHandler.remove();
		this._moveHandler = null;
	}
};
ptsElementBase.prototype._afterFullContentLoad = function() {
	//sthis.repositeMenu();
};
ptsElementBase.prototype._destroyMenu = function() {
	if(this._menu) {
		this._menu.destroy();
		this._menu = null;
	}
};
ptsElementBase.prototype.showMenu = function() {
	if(this._menu) {
		this._menu.show();
	}
};
ptsElementBase.prototype.hideMenu = function() {
	if(this._menu) {
		this._menu.hide();
	}
};
ptsElementBase.prototype.menuInAnimation = function() {
	if(this._menu) {
		return this._menu.inAnimation();
	}
	return false;
};
ptsElementBase.prototype.setMovable = function(state) {
	this._isMovable = state;
};
ptsElementBase.prototype.repositeMenu = function() {
	if(this._menu) {
		this._menu.reposite();
	}
};
/**
 * Text element
 */
function ptsElement_txt(jqueryHtml, block) {
	this._elId = null;
	this._editorElement = null;
	this._editor = null;
	this.includePostLinks = true;
	this._editorToolbarBtns = [
		['pts_editattrs'], ['pts_fontselect'], ['pts_fontsizeselect'], ['bold', 'italic', 'strikethrough'], ['pts_link'], ['forecolor']
	];
	ptsElement_txt.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElement_txt, ptsElementBase);
ptsElement_txt.prototype._init = function() {
	ptsElement_txt.superclass._init.apply(this, arguments);
	var id = this._$.attr('id')
	,	self = this;
	if(!id || id == '') {
		this._$.attr('id', 'ptsTxt_'+ mtRand(1, 99999));
	}
	var toolbarBtns = [];
	for(var i = 0; i < this._editorToolbarBtns.length; i++) {
		toolbarBtns.push( typeof(this._editorToolbarBtns[i]) === 'string' ? this._editorToolbarBtns[i] : this._editorToolbarBtns[i].join(' ') );
	}
	this._editorElement = this._$.tinymce({
		inline: true
	// ' |  | ' is panel buttons delimiter
	,	toolbar: toolbarBtns.join(' |  | ')
	,	menubar: false
	,	plugins: 'pts_editattrs pts_textcolor pts_link pts_fontselect pts_fontsizeselect'
	,	fontsize_formats: '8pt 10pt 12pt 14pt 18pt 24pt 36pt 48pt 64pt 72pt'
	,	skin : 'octo'
	,	convert_urls: false
	,	setup: function(ed) {
			this._editor = ed;
			ed.on('blur' ,function(e) {
				if(e.target._ptsChanged) {
					e.target._ptsChanged = false;
					_ptsSaveCanvas();
				}
				jQuery('.mce-container.mce-panel.mce-floatpanel.mce-menu:visible').hide();
			});
			ed.on('change', function(e) {
				e.target._ptsChanged = true;
				if(e.target._ptsChangeTimeout) {
					clearTimeout( e.target._ptsChangeTimeout );
				}
				e.target._ptsChangeTimeout = setTimeout(function(){
					self.getBlock().contentChanged();
				}, 1000);
			});
			ed.on('keyup', function(e) {
				var selectionCoords = getSelectionCoords();
				ptsMceMoveToolbar( self._editorElement.tinymce(), selectionCoords.x );
				self.getBlock().hideElementsMenus();
			});
			ed.on('click', function(e) {
				ptsMceMoveToolbar( self._editorElement.tinymce(), e.clientX );
				self.getBlock().hideElementsMenus();

				if (ed.theme.panel.hasOwnProperty('isInitPostlinkClick')) return;

				var handler = function () {
					ed.theme.panel.isInitPostlinkClick = true;

					var $fieldWp = jQuery('#' + self._$.attr('id') + 'ptsPostLinkList');

					if ($fieldWp.size()) {
						ed.theme.panel.off('click', handler);	
						
						self.initPostLinks($fieldWp.parents('.mce-container'));					
					}
				};

				ed.theme.panel.on('click', handler);
			});
			/*ed.on('focus', function(e) {
				
			});*/
			if(self._afterEditorInit) {
				self._afterEditorInit( ed );
			}
		}
	});
	this._$.removeClass('mce-edit-focus');
	// Do not allow drop anything it text element outside content area
	this._$.bind('dragover drop', function(event){
		event.preventDefault();
	});
};
ptsElement_txt.prototype.getEditorElement = function() {
	return this._editorElement;
};
ptsElement_txt.prototype.getEditor = function() {
	return this._editor;
};
ptsElement_txt.prototype.beforeSave = function() {
	ptsElement_txt.superclass.beforeSave.apply(this, arguments);
	if(!this._$) return;	// TODO: Make this work corect - if there are no html (_$) - then this method should not simple triggger. For now - it trigger even if _$ === null
	this._elId = this._$.attr('id');
	this._$
		.removeAttr('id')
		.removeAttr('contenteditable')
		.removeAttr('spellcheck')
		.removeClass('mce-content-body mce-edit-focus');
};
ptsElement_txt.prototype.afterSave = function() {
	ptsElement_txt.superclass.afterSave.apply(this, arguments);
	if(this._elId) {
		this._$
			.attr('id', this._elId)
			.attr('contenteditable', 'true')
			.attr('spellcheck', 'false')
			.addClass('mce-content-body');;
	}
};
/**
 * Image element
 */
function ptsElement_img(jqueryHtml, block) {
	if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = 'ptsElMenuImgExl';
	}
	this._menuClass = 'ptsElementMenu_img';
	this.includePostLinks = true;
	ptsElement_img.superclass.constructor.apply(this, arguments);
	var self = this;
	this._getImg().load(function(){
		self._block.contentChanged();
	});
}
extendPts(ptsElement_img, ptsElementBase);
ptsElement_img.prototype._beforeImgChange = function(opts, attach, imgUrl, imgToChange) {
	
};
ptsElement_img.prototype._afterImgChange = function(opts, attach, imgUrl, imgToChange) {
	
};
ptsElement_img.prototype._initMenuClbs = function() {
	ptsElement_img.superclass._initMenuClbs.apply(this, arguments);
	var self = this;
	this._menuClbs['.ptsImgChangeBtn'] = function() {
		self.set('type', 'img');
		self._getImg().show();
		self._getVideoFrame().remove();
		ptsCallWpMedia({
			id: self._$.attr('id')
		,	clb: function(opts, attach, imgUrl) {
				var imgToChange = self._getImg();
				self._block.beforeSave();
				self._innerImgsLoaded = 0;
				self._beforeImgChange( opts, attach, imgUrl, imgToChange );
				imgToChange.attr('src', imgUrl);
				self._afterImgChange( opts, attach, imgUrl, imgToChange );
				self._block.afterSave();
				self._block.contentChanged();
				_ptsSaveCanvas();
			}
		});
	};
	this._menuClbs['.ptsImgVideoSetBtn'] = function() {
		self.set('type', 'video');
		self._buildVideo( self._menu.$().find('[name=video_link]').val() );
	};
};
ptsElement_img.prototype._buildVideo = function(url) {
	url = url ? jQuery.trim( url ) : false;
	if(url) {
		var $editArea = this._getEditArea()
		,	$videoFrame = this._getVideoFrame( $editArea )
		,	$img = this._getImg( $editArea )
		,	src = ptsUtils.urlToVideoSrc( url );
		$videoFrame.attr({
			'src': src
		,	'width': $img.width()
		,	'height': $img.height()
		}).show();
		$img.hide();
	}
};
ptsElement_img.prototype._getVideoFrame = function( editArea ) {
	editArea = editArea ? editArea : this._getEditArea();
	var videoFrame = editArea.find('iframe.ptsVideo');
	if(!videoFrame.size()) {
		videoFrame = jQuery('<iframe class="ptsVideo" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen />').appendTo( editArea );
	}
	return videoFrame;
};
ptsElement_img.prototype._getImg = function(editArea) {
	editArea = editArea ? editArea : this._getEditArea();
	return editArea.find('img:first');
};
ptsElement_img.prototype._initMenu = function() {
	ptsElement_img.superclass._initMenu.apply(this, arguments);
	var self = this;
	this._menu.$().find('[name=video_link]').change(function(){
		self._buildVideo( jQuery(this).val() );
	}).keyup(function(e){
		if(e.keyCode == 13) {	// Enter
			self._buildVideo( jQuery(this).val() );
		}
	});
};
ptsElement_img.prototype._getLink = function() {
	var $link = this._$.find('a.ptsLink');
	return $link.size() ? $link : false;
};
ptsElement_img.prototype._setLinkAttr = function(attr, val) {
	switch(attr) {
		case 'href':
			if(val) {
				var $link = this._createLink();
				$link.attr(attr, val);
			} else
				this._removeLink();
			break;
		case 'title':
			var $link = this._createLink();
			$link.attr(attr, val);
			break;
		case 'target':
			var $link = this._createLink();
			val ? $link.attr('target', '_blank') : $link.removeAttr('target');
			break;
	}
};
ptsElement_img.prototype._createLink = function() {
	var $link = this._getLink();
	if(!$link) {
		$link = jQuery('<a class="ptsLink" />').append( this._$.find('img') ).appendTo( this._getEditArea() );
		$link.click(function(e){
			e.preventDefault();
		});
	}
	return $link;
};
ptsElement_img.prototype._removeLink = function() {
	var $link = this._getLink();
	if($link) {
		this._getEditArea().append( this._$.find('img') );
		$link.remove();
	}
};
ptsElement_img.prototype._isRelNofollow = function (nofollow) {
	var $link = this._getLink();

	if (!$link)
		$link = this._createLink();

	if (nofollow)
		$link.attr('rel', 'nofollow');
	else
		$link.removeAttr('rel');
};
/**
 * Gallery image element
 */
function ptsElement_gal_img(jqueryHtml, block) {
	if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = 'ptsElMenuGalItemExl';
	}
	ptsElement_gal_img.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElement_gal_img, ptsElement_img);
ptsElement_gal_img.prototype._afterDestroy = function() {
	ptsElement_gal_img.superclass._afterDestroy.apply(this, arguments);
	this._block.recalcRows();
};
ptsElement_gal_img.prototype._afterImgChange = function(opts, attach, imgUrl, imgToChange) {
	ptsElement_gal_img.superclass._afterImgChange.apply(this, arguments);
	imgToChange.attr('data-full-img', attach.url);
	imgToChange.parents('.ptsGalLink:first').attr('href', attach.url);
};
/**
 * Menu item element
 */
function ptsElement_menu_item(jqueryHtml, block) {
	/*if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = 'ptsElMenuGalItemExl';
	}*/
	ptsElement_menu_item.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElement_menu_item, ptsElement_txt);
ptsElement_menu_item.prototype._afterEditorInit = function(editor) {
	var self = this;
	editor.addButton('tables_remove', {
		title: 'Remove'
	,	onclick: function(e) {
			self.destroy();
		}
	});
};
ptsElement_menu_item.prototype._beforeInit = function() {
	this._editorToolbarBtns.push('tables_remove');
};

/**
 * Menu item image
 */
function ptsElement_menu_item_img(jqueryHtml, block) {
	if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = 'ptsElMenuMenuItemImgExl';
	}
	ptsElement_menu_item_img.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElement_menu_item_img, ptsElement_img);
/**
 * Input item
 */
function ptsElement_input(jqueryHtml, block) {
	if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = 'ptsElMenuInputExl';
	}
	ptsElement_input.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElement_input, ptsElementBase);
ptsElement_input.prototype._init = function() {
	ptsElement_input.superclass._init.apply(this, arguments);
	var saveClb = function(element) {
		jQuery(element).attr('placeholder', jQuery(element).val());
		jQuery(element).val('');
		_ptsSaveCanvasDelay();
	};
	this._getInput().focus(function(){
		jQuery(this).val(jQuery(this).attr('placeholder'));
	}).blur(function(){
		if(jQuery(this).data('saved')) {
			jQuery(this).data('saved', 0);
			return;
		}
		saveClb(this)
	}).keyup(function(e){
		if(e.keyCode == 13) {	// Enter
			saveClb(this);
			jQuery(this).data('saved', 1).trigger('blur');	// We must blur from element after each save in any case
		}
	});
};
ptsElement_input.prototype._getInput = function() {
	if(!this._$) return;	// TODO: Make this work corect - if there are no html (_$) - then this method should not simple triggger. For now - it trigger even if _$ === null
	// TODO: Modify this to return all fields types
	return this._$.find('input');
};
ptsElement_input.prototype._initMenu = function(){
	ptsElement_input.superclass._initMenu.apply(this, arguments);
	if(!this._$) return;	// TODO: Make this work corect - if there are no html (_$) - then this method should not simple triggger. For now - it trigger even if _$ === null
	var self = this
	,	menuReqCheck = this._menu.$().find('[name="input_required"]');
	menuReqCheck.change(function(){
		var required = jQuery(this).attr('checked');
		if(required) {
			self._getInput().attr('required', '1');
		} else {
			self._getInput().removeAttr('required');
		}
		self._block.setFieldRequired(self._getInput().get(0).name, (helperChecked ? 1 : 0));
		_ptsSaveCanvasDelay();
	});
	self._getInput().attr('required')
		? menuReqCheck.attr('checked', 'checked')
		: menuReqCheck.removeAttr('checked');
	ptsCheckUpdate( menuReqCheck );
};
ptsElement_input.prototype.destroy = function() {
	// Remove field from block fields list at first
	var name = this._getInput().attr('name');
	this._block.removeField( name );
	ptsElement_input.superclass.destroy.apply(this, arguments);
};
/**
 * Input button item
 */
function ptsElement_input_btn(jqueryHtml, block) {
	if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = 'ptsElMenuInputBtnExl';
	}
	ptsElement_input_btn.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElement_input_btn, ptsElementBase);
ptsElement_input_btn.prototype._getInput = function() {
	// TODO: Modify this to return all fields types
	return this._$.find('input');
};
ptsElement_input_btn.prototype._init = function() {
	ptsElement_input_btn.superclass._init.apply(this, arguments);
	var saveClb = function(element) {
		jQuery(element).attr('type', 'submit');
		_ptsSaveCanvasDelay();
	};
	this._getInput().click(function(){
		return false;
	}).focus(function(){
		var value = jQuery(this).val();
		jQuery(this).attr('type', 'text').val( value );
	}).blur(function(){
		if(jQuery(this).data('saved')) {
			jQuery(this).data('saved', 0);
			return;
		}
		saveClb(this);
	}).keyup(function(e){
		if(e.keyCode == 13) {	// Enter
			saveClb(this);
			jQuery(this).data('saved', 1).trigger('blur');	// We must blur from element after each save in any case
		}
	});
};
/**
 * Standart button item
 */
ptsElement_btn.prototype.beforeSave = function() {
	ptsElement_btn.superclass.beforeSave.apply(this, arguments);
	this._getEditArea().removeAttr('contenteditable');
};
ptsElement_btn.prototype.afterSave = function() {
	ptsElement_btn.superclass.afterSave.apply(this, arguments);
	this._getEditArea().attr('contenteditable', true);
};
ptsElement_btn.prototype._init = function() {
	ptsElement_btn.superclass._init.apply(this, arguments);
	var self = this;
	this._getEditArea().attr('contenteditable', true).blur(function(){
		self._block.contentChanged();
		//_ptsSaveCanvasDelay();
	}).keypress(function(e){
		if(e.keyCode == 13 && window.getSelection) {	// Enter
			document.execCommand('insertHTML', false, '<br>');
			if (typeof e.preventDefault != "undefined") {
                e.preventDefault();
            } else {
                e.returnValue = false;
            }
		}
	});
	if(this.get('customhover-clb')) {

	}
};
ptsElement_btn.prototype._setColor = function(color) {
	this.set('bgcolor', color);
	var bgElements = this.get('bgcolor-elements');
	if(bgElements)
		bgElements = this._$.find(bgElements);
	else
		bgElements = this._$;
	switch(this.get('bgcolor-to')) {
		case 'border':	// Change only borders color
			bgElements.css({
				'border-color': color
			});
			break;
		case 'txt':
			bgElements.css({
				'color': color
			});
			break;
		case 'bg':
		default:
			bgElements.css({
				'background-color': color
			});
			break;
	}
	if(this._haveAdditionBgEl === null) {
		this._haveAdditionBgEl = this._$.find('.ptsAddBgEl');
		if(!this._haveAdditionBgEl.size()) {
			this._haveAdditionBgEl = false;
		}
	}
	if(this._haveAdditionBgEl) {
		this._haveAdditionBgEl.css({
			'background-color': color
		});
	}
	if(this.get('bgcolor-clb')) {
		var clbName = this.get('bgcolor-clb');
		if(typeof(this[clbName]) === 'function') {
			this[clbName]( color );
		}
	}
};
/**
 * Icon item
 */
function ptsElement_icon(jqueryHtml, block) {
	if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = 'ptsElMenuIconExl';
	}
	this._menuClass = 'ptsElementMenu_icon';
	this.includePostLinks = true;
	ptsElement_icon.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElement_icon, ptsElementBase);
ptsElement_icon.prototype._setColor = function(color) {
	this.set('color', color);
	this._getEditArea().css('color', color);
};
ptsElement_icon.prototype._getLink = function() {
	var $link = this._$.find('a.ptsLink');
	return $link.size() ? $link : false;
};
ptsElement_icon.prototype._setLinkAttr = function(attr, val) {
	switch(attr) {
		case 'href':
			if(val) {
				var $link = this._createLink();
				$link.attr(attr, val);
			} else
				this._removeLink();
			break;
		case 'title':
			var $link = this._createLink();
			$link.attr(attr, val);
			break;
		case 'target':
			var $link = this._createLink();
			val ? $link.attr('target', '_blank') : $link.removeAttr('target');
			break;
	}
};
ptsElement_icon.prototype._createLink = function() {
	var $link = this._getLink();
	if(!$link) {
		$link = jQuery('<a class="ptsLink" />').append( this._$.find('.ptsInputShell') ).appendTo( this._$ );
		$link.click(function(e){
			e.preventDefault();
		});
	}
	return $link;
};
ptsElement_icon.prototype._removeLink = function() {
	var $link = this._getLink();
	if($link) {
		this._$.append( $link.find('.ptsInputShell') );
		$link.remove();
	}
};
ptsElement_icon.prototype._isRelNofollow = function (nofollow) {
	var $link = this._getLink();

	if (!$link)
		$link = this._createLink();

	if (nofollow)
		$link.attr('rel', 'nofollow');
	else
		$link.removeAttr('rel');
};
/**
 * Table column element
 */
ptsElement_table_col.prototype._setColor = function(color) {
	if(color) {
		this.set('color', color);
	} else {
		color = this.get('color');
	}
	var enbColor = parseInt(this.get('enb-color'))
	,	block = this.getBlock()
	,	colNum = this._colNum
	,	cssTag = 'col color '+ colNum
	,	cellColorCss = block.getParam('cell_color_css')
	,	useCss = cellColorCss && cellColorCss !== '';
	if(enbColor) {
		if(useCss) {
			block.setTaggedStyle(block.getParam('cell_color_css'), cssTag, {num: colNum, color: color});
		} else {
			var $bgColorTo = this._$.find('[data-bg-to]')
			,	firstBgColor = this.get('first-bg-color');
			if(!firstBgColor) {
				this.set('first-bg-color', $bgColorTo.css('background-color'));
			}
			$bgColorTo.css('background-color', color);
		}
	} else {
		if(useCss) {
			block.removeTaggedStyle(cssTag);
		} else {
			var $bgColorTo = this._$.find('[data-bg-to]');
			$bgColorTo.css('background-color', this.get('first-bg-color'));
		}
	}
	//_ptsSaveCanvas();
};
ptsElement_table_col.prototype._setColNum = function(num) {
	this._colNum = num;
};
ptsElement_table_col.prototype._afterDestroy = function() {
	ptsElement_table_col.superclass._afterDestroy.apply(this, arguments);
	this._block.checkColWidthPerc();
};
ptsElement_table_col.prototype._showSelectBadgeWnd = function() {
	this.hideMenu();
	ptsUtils.showBadgesLibWnd( this );
};
ptsElement_table_col.prototype._disableBadge = function() {
	this._getBadgeHtml().hide();
};
ptsElement_table_col.prototype._setBadge = function(data) {
	if(data) {
		for(var key in data) {
			this.set('badge-'+ key, data[ key ]);
		}
	} else {
		data = this._getBadgeData();
	}
	if(!data) return;

	ptsUtils.updateBadgePrevLib( this._getBadgeHtml().show(), data );
	this.set('enb-badge', 1);
	var $enbBadgeCheck = this._menu.$().find('[name=enb_badge_col]');
	$enbBadgeCheck.attr('checked', 'checked');
	ptsCheckUpdate( $enbBadgeCheck );
};
ptsElement_table_col.prototype._getBadgeData = function() {
	var keys = ['badge_name', 'badge_bg_color', 'badge_txt_color', 'badge_pos']
	,	data = {};
	for(var i = 0; i < keys.length; i++) {
		data[ keys[i] ] = this.get('badge-'+ keys[ i ]);
		if(!data[ keys[i] ])
			return false;
	}
	return data;
};
ptsElement_table_col.prototype._getBadgeHtml = function() {
	var $badge = this._$.find('.ptsColBadge');
	if(!$badge.size()) {
		$badge = jQuery('<div class="ptsColBadge"><div class="ptsColBadgeContent"></div></div>').appendTo( this._getEditArea() );
	}
	return $badge;
};
/**
 * Table description column element
 */
ptsElement_table_col_desc.prototype._initMenu = function() {
	ptsElement_table_col_desc.superclass._initMenu.apply(this, arguments);
	// Column description created from usual table column element, with it's menu.
	// But we can't move or remove (we can hide this from block settings) this type of column, so let's just remove it's move handle from menu.
	var $moveHandle = this._menu.$().find('.ptsElMenuMoveHandlerPlace')
	,	$removeBtn = this._menu.$().find('.ptsRemoveElBtn');
	$moveHandle.next('.ptsElMenuBtnDelimiter').remove();
	$moveHandle.remove();
	$removeBtn.prev('.ptsElMenuBtnDelimiter').remove();
	$removeBtn.remove();
	this._menu.$().css('min-width', '130px');
};
/**
 * Table cell element
 */
function ptsElement_table_cell(jqueryHtml, block) {
	if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = 'ptsElMenuTableCellExl';
	}
	this._menuClass = 'ptsElementMenu_table_cell';
	ptsElement_table_cell.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElement_table_cell, ptsElementBase);
ptsElement_table_cell.prototype._initMenuClbs = function() {
	ptsElement_table_cell.superclass._initMenuClbs.apply(this, arguments);
	var self = this;
	this._menuClbs['.ptsTypeTxtBtn'] = function() {
		self._replaceElement('txt_cell_item', 'txt');
	};
	this._menuClbs['.ptsTypeImgBtn'] = function() {
		self._replaceElement('img_cell_item', 'img');
	};
	this._menuClbs['.ptsTypeIconBtn'] = function() {
		self._replaceElement('icon_cell_item', 'icon');
	};
	this._menuClbs['.ptsTypeButtonBtn'] = function() {
		self._replaceElement('icon_cell_item', 'btn');
	};
	
};
ptsElement_table_cell.prototype._replaceElement = function(toParamCode, type) {
	var editArea = this._getEditArea()
	,	elementIter = editArea.find('.ptsEl').data('iter-num')
	,	block = this.getBlock();
	// Destroy current element in cell
	block.destroyElementByIterNum( elementIter );
	// Add new one
	editArea.html( block.getParam( toParamCode ) );
	block._initElementsForArea( editArea );
	this.set('type', type);
	this._menu.$().find('[name=type]').removeAttr('checked').filter('[value='+ type+ ']').attr('checked', 'checked');
};
/**
 * Table Cell Icon element
 */
function ptsElement_table_cell_icon(jqueryHtml, block) {
	if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = 'ptsElMenuTableCellIconExl';
	}
	this._changeable = true;
	this.includePostLinks = true;
	ptsElement_table_cell_icon.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElement_table_cell_icon, ptsElement_icon);
/**
 * Table Cell Image element
 */
function ptsElement_table_cell_img(jqueryHtml, block) {
	if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = 'ptsElMenuTableCellImgExl';
	}
	this._changeable = true;
	this.includePostLinks = true;
	ptsElement_table_cell_img.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElement_table_cell_img, ptsElement_img);
/**
 * Table Cell Image element
 */
function ptsElement_table_cell_txt(jqueryHtml, block) {
	this._typeBtns = {
		pts_el_menu_type_txt: {
			text: toeLangPts('Text')
		,	type: 'txt'
		,	checked: true
		}
	,	pts_el_menu_type_img: {
			text: toeLangPts('Image / Video')
		,	type: 'img'
		}
	,	pts_el_menu_type_icon: {
			text: toeLangPts('Icon')
		,	type: 'icon'
		}
	,	pts_el_menu_type_btn: {
			text: toeLangPts('Button')
		,	type: 'btn'
		}
	};
	this.includePostLinks = true;
	ptsElement_table_cell_txt.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElement_table_cell_txt, ptsElement_txt);
ptsElement_table_cell_txt.prototype._afterEditorInit = function(editor) {
	var self = this;

	var onclickClb = function() {
		
		var $btn = jQuery('#'+ this._id).find('button:first')
		,	$btnsGroupShell = $btn.parents('.mce-container.mce-btn-group:first')
		,	$radio = $btn.find('input[type=radio]')
		,	type = $radio.val();
		if(type === 'txt') return;
		
		$btnsGroupShell.find('input[type=radio]').removeAttr('checked');
		$radio.attr('checked') 
			? $radio.removeAttr('checked')
			: $radio.attr('checked', 'checked');
		ptsCheckUpdateArea( $btnsGroupShell );
		// And now - let's make element change
		var element = this.settings._ptsElement;

		element.getBlock().replaceElement(element, type+ '_item_html', type);
	},	onPostRenderClb = function(type, checked) {
		
		var $btnShell = jQuery('#'+ this._id)
		,	$btn = $btnShell.find('button:first')
		,	txt = $btn.html()
		,	$radioHtml = jQuery('<label><input type="radio" name="type" value="'+ type+ '" '+ (checked ? 'checked' : '')+' />'+ txt+ '</label>');
		
		$btn.html('').append( $radioHtml );
		$radioHtml.find('input').change( jQuery.proxy(onclickClb, this) );
		ptsInitCustomCheckRadio( $btn );
	};
	for(var btnKey in this._typeBtns) {
		editor.addButton(btnKey, {
			text: this._typeBtns[ btnKey ].text
		,	_ptsType: this._typeBtns[ btnKey ].type
		,	_ptsChecked: this._typeBtns[ btnKey ].checked
		,	_ptsElement: this
		,	classes: 'btn'
		,	onclick: function() {	// see onPostRenderClb() - $radioHtml.find('input').change()
				jQuery.proxy(onclickClb, this)();
			}
		,	onpostrender: function(e) {
				jQuery.proxy(onPostRenderClb, this)(this.settings._ptsType, this.settings._ptsChecked);
			}
		});
	}

	editor.addButton('remove', {
			_ptsElement: this
		,	icon: 'remove fa fa-trash-o'
		,	classes: 'btn'
		,	onclick: function() {
				self.destroy();
			}
		});
};
ptsElement_table_cell_txt.prototype._beforeInit = function() {
	var btnsPack = [];
	for(var btnKey in this._typeBtns) {
		btnsPack.push( btnKey );
	}
	btnsPack.push( 'remove' );
	this._editorToolbarBtns.push( btnsPack );
};



