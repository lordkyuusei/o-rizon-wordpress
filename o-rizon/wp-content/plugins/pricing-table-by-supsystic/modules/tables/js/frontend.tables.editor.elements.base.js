function ptsElementBase(jqueryHtml, block) {
	this._iterNum = 0;
	this._id = 'el_'+ mtRand(1, 999999);
	this._animationSpeed = g_ptsAnimationSpeed;
	this._$ = jqueryHtml;
	this._block = block;
	if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = '';
	}
	this._innerImgsCount = 0;
	this._innerImgsLoaded = 0;
	//this._$menu = null;
	this._menu = null;
	this._menuClbs = {};
	if(typeof(this._menuClass) === 'undefined') {
		this._menuClass = 'ptsElementMenu';
	}
	this._menuOnBottom = false;
	this._code = 'base';

	this._initedComplete = false;
	this._editArea = null;
	if(typeof(this._isMovable) === 'undefined') {
		this._isMovable = false;
	}
	this._moveHandler = null;
	this._sortInProgress = false;
	if(typeof(this._showMenuEvent) === 'undefined') {
		this._showMenuEvent = 'click';
	}
	if(typeof(this._changeable) === 'undefined') {
		this._changeable = false;
	}
	if(g_ptsEdit) {
		this._init();
		this._initMenuClbs();
		this._initMenu();

		var images = this._$.find('img');
		if(images && (this._innerImgsCount = images.size())) {
			this._innerImgsLoaded = 0;
			var self = this;
			images.load(function(){
				self._innerImgsLoaded++;
				if(self._$.find('img').size() == self._innerImgsLoaded) {
					self._afterFullContentLoad();
				}
			});
		}
	}
	this._onlyFirstHtmlInit();
	this._initedComplete = true;
}
ptsElementBase.prototype.getId = function() {
	return this._id;
};
ptsElementBase.prototype.getBlock = function() {
	return this._block;
};
ptsElementBase.prototype._onlyFirstHtmlInit = function() {
	if(this._$ && !this._$.data('first-inited')) {
		this._$.data('first-inited', 1);
		return true;
	}
	return false;
};
ptsElementBase.prototype.setIterNum = function(num) {
	this._iterNum = num;
	this._$.data('iter-num', num);
};
ptsElementBase.prototype.getIterNum = function() {
	return this._iterNum;
};
ptsElementBase.prototype.$ = function() {
	return this._$;
};
ptsElementBase.prototype.getCode = function() {
	return this._code;
};
ptsElementBase.prototype._setCode = function(code) {
	this._code = code;
};
ptsElementBase.prototype._init = function() {
	this._beforeInit();
};
ptsElementBase.prototype._beforeInit = function() {
	
};
ptsElementBase.prototype.destroy = function() {
	
};
ptsElementBase.prototype.get = function(opt) {
	return jQuery('<div/>').html(this._$.attr( 'data-'+ opt )).text();	// not .data() - as it should be saved even after page reload, .data() will not create element attribute
};
ptsElementBase.prototype.set = function(opt, val) {
	this._$.attr( 'data-'+ opt, jQuery('<div/>').text(val).html() );	// not .data() - as it should be saved even after page reload, .data() will not create element attribute
};
ptsElementBase.prototype._getEditArea = function() {
	if(!this._editArea) {
		this._editArea = this._$.children('.ptsElArea');
		if(!this._editArea.size()) {
			this._editArea = this._$.find('.ptsInputShell');
		}
	}
	return this._editArea;
};
ptsElementBase.prototype._getOverlay = function() {
	return this._$.find('.ptsElOverlay');
};
/**
 * Standart button item
 */
function ptsElement_btn(jqueryHtml, block) {
	if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = 'ptsElMenuBtnExl';
	}
	this._menuClass = 'ptsElementMenu_btn';
	this._haveAdditionBgEl = null;
	this._changeable = true;
	this.includePostLinks = true;
	ptsElement_btn.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElement_btn, ptsElementBase);
ptsElement_btn.prototype._onlyFirstHtmlInit = function() {
	if(ptsElement_btn.superclass._onlyFirstHtmlInit.apply(this, arguments)) {
		if(this.get('customhover-clb')) {
			var clbName = this.get('customhover-clb');
			if(typeof(this[clbName]) === 'function') {
				var self = this;
				this._getEditArea().hover(function(){
					self[clbName](true, this);
				}, function(){
					self[clbName](false, this);
				});
			}
		}
	}
};
ptsElement_btn.prototype._hoverChangeFontColor = function( hover, element ) {
	if(hover) {
		jQuery(element)
			.data('original-color', this._getEditArea().css('color'))
			.css('color', jQuery(element).parents('.ptsEl:first').attr('data-bgcolor'));	// Ugly, but only one way to get this value in dynamic way for now
	} else {
		jQuery(element)
			.css('color', jQuery(element).data('original-color'));
	}
};
ptsElement_btn.prototype._hoverChangeBgColor = function( hover, element ) {
	var parentElement = jQuery(element).parents('.ptsEl:first');	// Actual element html
	if(hover) {
		parentElement
			.data('original-color', parentElement.css('background-color'))
			.css('background-color', parentElement.attr('data-bgcolor'));	// Ugly, but only one way to get this value in dynamic way for now
	} else {
		parentElement
			.css('background-color', parentElement.data('original-color'));
	}
};
ptsElement_btn.prototype._hoverBorderColor = function( hover, element ) {
	//var parentElement = jQuery(element).parents('.ptsEl:first');	// Actual element html
	if(hover) {
		jQuery(element)
			.data('original-color', jQuery(element).css('border-color'))
			.css('border-color', jQuery(element).parents('.ptsEl:first').attr('data-bgcolor'));	// Ugly, but only one way to get this value in dynamic way for now
	} else {
		jQuery(element)
			.css('border-color', jQuery(element).data('original-color'));
	}
};
/**
 * Table column element
 */
function ptsElement_table_col(jqueryHtml, block) {
	if(typeof(this._menuOriginalId) === 'undefined') {
		this._menuOriginalId = 'ptsElMenuTableColExl';
	}
	if(typeof(this._menuClass) === 'undefined') {
		this._menuClass = 'ptsElementMenu_table_col';
	}
	if(typeof(this._isMovable) === 'undefined') {
		this._isMovable = true;
	}
	this._showMenuEvent = 'hover';
	this._colNum = 0;
	ptsElement_table_col.superclass.constructor.apply(this, arguments);
	/*if(parseInt(this.get('enb-schedule'))) {
		console.log(this.getIterNum());
	}*/
}
extendPts(ptsElement_table_col, ptsElementBase);
ptsElement_table_col.prototype.setIterNum = function() {
	ptsElement_table_col.superclass.setIterNum.apply(this, arguments);
	if(!g_ptsEdit && typeof(ptsScheduleCheck) !== 'undefined') {
		ptsScheduleCheck(this);
	}
};
/**
 * Table description column element
 */
function ptsElement_table_col_desc(jqueryHtml, block) {
	this._isMovable = false;
	ptsElement_table_col_desc.superclass.constructor.apply(this, arguments);
	//this.refreshHeight();
	//var self = this;
	/*this.getBlock().$().bind('ptsBlockContentChanged', function(){
		self.refreshHeight();
	});*/
}
extendPts(ptsElement_table_col_desc, ptsElement_table_col);
/*ptsElement_table_col_desc.prototype.refreshHeight = function() {
	var sizes = this.getBlock().getMaxColsSizes();
	for(var key in sizes) {
		var $entity = this._$.find(sizes[ key ].sel);
		if($entity && $entity.size()) {
			if(key == 'cells' &&  sizes[ key ].height) {
				var cellNum = 0;
				$entity.each(function(){
					if(typeof(sizes[ key ].height[ cellNum ]) !== 'undefined') {
						jQuery(this).css('height', sizes[ key ].height[ cellNum ]);
					}
					cellNum++;
				});
			} else {
				$entity.css('height', sizes[ key ].height);
			}
		}
	}
};*/

/**
 * Text element
 */
function ptsElement_table_cell_txt(jqueryHtml, block) {
	if (block.getParam('responsive_text')) {
		jqueryHtml.find('span, p').responsiveText({ minFontSize: 14 });
	}
	this.includePostLinks = true;
	ptsElement_table_cell_txt.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElement_table_cell_txt, ptsElementBase);