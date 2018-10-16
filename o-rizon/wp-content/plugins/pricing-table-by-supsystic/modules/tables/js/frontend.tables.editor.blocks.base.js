/**
 * Base block object - for extending
 * @param {object} blockData all block data from database (block database row)
 */
function ptsBlockBase(blockData) {
	this._data = blockData;
	this._$ = null;
	this._original$ = null;
	this._id = 0;
	this._iter = 0;
	this._elements = [];
	this._animationSpeed = 300;
	this._disableContentChange = false;
	//this._oneTimeElementsInited = false;
}
ptsBlockBase.prototype.get = function(key) {
	return this._data[ key ];
};
ptsBlockBase.prototype.getParam = function(key) {
	return this._data.params[ key ] ? this._data.params[ key ].val : false;
};
ptsBlockBase.prototype.setParam = function(key, value) {
	if(!this._data.params[ key ]) this._data.params[ key ] = {};
	this._data.params[ key ].val = value;
};
ptsBlockBase.prototype.getRaw = function() {
	return this._$;
};
/**
 * Alias for getRaw method
 */
ptsBlockBase.prototype.$ = function() {
	return this.getRaw();
};
ptsBlockBase.prototype.setRaw = function(jqueryHtml) {
	this._$ = jqueryHtml;
	this._resetElements();
	this._initHtml();
	if(this.getParam('font_family')) {
		this._setFont( this.getParam('font_family') );
	}
};
ptsBlockBase.prototype._initElements = function() {
	this._initElementsForArea( this._$ );
};
ptsBlockBase.prototype._initElementsForArea = function(area) {
	var block = this
	,	addedElements = [];
	var initElement = function(htmlEl) {
		var elementCode = jQuery(htmlEl).data('el')
		,	elementClass = window[ 'ptsElement_'+ elementCode ];
		if(elementClass) {
			var newElement = new elementClass(jQuery(htmlEl), block);
			newElement._setCode(elementCode);
			var newIterNum = block._elements.push( newElement );
			addedElements.push( newElement );
			newElement.setIterNum( newIterNum - 1 );	// newIterNum == new length of _elements array, iterator number for element - is new length - 1
		} else {
			if(g_ptsEdit)
				console.log('Undefined Element ['+ elementCode+ '] !!!');
		}
	};
	jQuery( area ).find('.ptsEl').each(function(){
		initElement(this);
	});
	if(jQuery( area ).hasClass('ptsEl')) {
		initElement( area );
	}
	this._afterInitElements();
	return addedElements;
};
ptsBlockBase.prototype._afterInitElements = function() {
};
ptsBlockBase.prototype._resetElements = function() {
	this._clearElements();
	this._initElements();
};
ptsBlockBase.prototype._clearElements = function() {
	if(this._elements && this._elements.length) {
		for(var i = 0; i < this._elements.length; i++) {
			this._elements[ i ].destroy();
		}
		this._elements = [];
	}
};
ptsBlockBase.prototype.getElements = function() {
	return this._elements;
};
ptsBlockBase.prototype._initHtml = function() {

};
/**
 * ID number in list of canvas elements
 * @param {numeric} iter Iterator - number in all blocks array - for this element
 */
ptsBlockBase.prototype.setIter = function(iter) {
	this._iter = iter;
};
ptsBlockBase.prototype.showLoader = function(txt) {
	var loaderHtml = jQuery('#ptsBlockLoader');
	txt = txt ? txt : loaderHtml.data('base-txt');
	loaderHtml.find('.ptsBlockLoaderTxt').html( txt );
	loaderHtml.css({
		'height': this._$.height()
	,	'top': this._$.offset().top
	}).addClass('active');
};
ptsBlockBase.prototype.hideLoader = function() {
	var loaderHtml = jQuery('#ptsBlockLoader');
	loaderHtml.removeClass('active');
};
ptsBlockBase.prototype._setFont = function(fontFamily) {
	var $fontLink = this._getFontLink();
	// If this is not standard font - it should be Google font, so - load it from Google Fonts server here

	if(toeInArrayPts(fontFamily, ptsBuildConst.standardFonts) === false) {
		$fontLink.attr({
			'href': 'https://fonts.googleapis.com/css?family='+ encodeURIComponent(fontFamily)
		//,	'data-font-family': fontFamily
		});
	}
	this._$.css({
		'font-family': fontFamily
	});
	this.setParam('font_family', fontFamily);
};
ptsBlockBase.prototype._getFontLink = function() {
	var $link = this._$.find('link.ptsFont');
	if(!$link.size()) {
		$link = jQuery('<link class="ptsFont" rel="stylesheet" type="text/css"/>').appendTo( this._$ );
	}
	return $link;
};
ptsBlockBase.prototype.getElementByIterNum = function(iterNum) {
	return this._elements[ iterNum ];
};
ptsBlockBase.prototype.removeElementByIterNum = function(iterNum) {
	this._elements.splice( iterNum, 1 );
	if(this._elements && this._elements.length) {
		for(var i = 0; i < this._elements.length; i++) {
			this._elements[ i ].setIterNum( i );
		}
	}
};
ptsBlockBase.prototype.destroyElementByIterNum = function(iterNum, clb) {
	this.getElementByIterNum( iterNum ).destroy( clb );	// It will call removeElementByIterNum() inside element destroy method
};
/**
 * Price table block base class
 */
function ptsBlock_price_table(blockData) {
	this._increaseHoverFontPerc = 20;	// Increase font on hover effect, %
	this._$lastHoveredCol = null;
	this._refreshColsBinded = false;
	this._onloadHandle = false;
	this._isAlreadyShowed = false;
	this._isResponsiveDescInit = false;
	ptsBlock_price_table.superclass.constructor.apply(this, arguments);
}
extendPts(ptsBlock_price_table, ptsBlockBase);
ptsBlock_price_table.prototype._getColsContainer = function() {
	return this._$.find('.ptsColsWrapper:first');
};
ptsBlock_price_table.prototype._getCols = function(includeDescCol) {
	return this._getColsContainer().find('.ptsCol'+ (includeDescCol ? '' : ':not(.ptsTableDescCol)'));
};
ptsBlock_price_table.prototype._afterInitElements = function() {
	ptsBlock_price_table.superclass._afterInitElements.apply(this, arguments);
	if(parseInt(this.getParam('enb_hover_animation'))) {
		this._initHoverEffect();
	}
	if (this.getParam('table_align'))
		this._$.addClass('ptsTableAlign_' + this.getParam('table_align'));
	if (this.getParam('text_align'))
		this._$.addClass('ptsAlign_' + this.getParam('text_align'));
	if(!this._disableContentChange) {
		this._refreshCellsHeight();
	}
	if(!this._refreshColsBinded) {
		this._$.bind('ptsBlockContentChanged', jQuery.proxy(function(){
			this._refreshCellsHeight();
		}, this));
		this._refreshColsBinded = true;
	}
	if(!_ptsIsEditMode()) { // Not for edit mode unfortunatelly ....
		var $tooltipstedCells = this._$.find('.ptsCell[title]');

		if(this.getParam('disable_custom_tooltip_style') != '1') {
			if($tooltipstedCells && $tooltipstedCells.size()) {
				// TODO: Move this to Options and make this part more flexible
				var tooltipsterSettings = {
					contentAsHTML: true
					,	interactive: true
					,	speed: 250
					,	delay: 0
					,	animation: 'swing'
					,	maxWidth: 450
					,	position: 'top'
				};
				$tooltipstedCells.tooltipster( tooltipsterSettings );
			}
		}
		var self = this
		,	PTS_VISIBLE_SET_HEIGHT_KEY = 'PTS-VISIBLE-SET-HEIGHT-KEY';

		this._fixResponsive();

		jQuery(window).resize(function(){
			if (! self._$.is(':visible')) {
				self._$.data(PTS_VISIBLE_SET_HEIGHT_KEY, false);
			} else {
				self._fixResponsive();
				self._refreshCellsHeight();
			}
		});
		jQuery(window).on('toggleChangeCellHeight', function () {
			self._refreshCellsHeight();
		});
		jQuery(function () {
			var isEnableLazyLoad = function () { 
				return self._$.find('img[data-lazy-src]:not(.lazyloaded)').size() > 0;
			}, checkedLazyLoadLib = false;

			if (isEnableLazyLoad())
				checkedLazyLoadLib = true;

			document.body.addEventListener("DOMSubtreeModified", function (e) {
				if (checkedLazyLoadLib 
					&& e.target.nodeType == 1 
					&& e.target.nodeName == "IMG" 
					&& jQuery.contains(self._$.get(0), e.target)
				) {
					var isLoadedImages = true;

					self._$.find('img[data-lazy-src]').each(function () {
						var $this = jQuery(this);

						if (!$this.hasClass('lazyloaded') && !$this.hasClass('lazyload'))
							isLoadedImages = false;
					});

					if (isLoadedImages) {
						checkedLazyLoadLib = false;

						self._$.find('img[data-lazy-src]').on('load', function () {
							self._fixResponsive();
							self._refreshCellsHeight();
						});
					}
				}

				if (self._isAlreadyShowed || ! self._$) return;

				if (self._$.visible()) {
			  		self._isAlreadyShowed = true;
			  		self._fixResponsive();
			  		self._refreshCellsHeight();
			  	}
			}, false);

			self.setCalcWidth();
		});
	} else {
		this.columnChidrensWidthCalc();
	}

	var self = this;

	if (! this._onloadHandle) {
		this._onloadHandle  = true;
		jQuery(window).load(function(){
			self._refreshCellsHeight();
		});
	}
};
ptsBlock_price_table.prototype._initHoverEffect = function() {
	if(_ptsIsEditMode()) {
		this.setParam('enb_hover_animation', 1);
		return;
	}
	var $cols = this._getCols()
	,	self = this;
	this._disableHoverEffect( $cols );
	$cols.bind('hover.animation', function(e){
		switch(e.type) {
			case 'mouseenter': case 'mousein':
				self._increaseHoverFont( jQuery(this) );
				break;
			case 'mouseleave': case 'mouseout':
				self._backHoverFont( jQuery(this) );
				break;
		}
	});
	this.setParam('enb_hover_animation', 1);
};
ptsBlock_price_table.prototype._increaseHoverFont = function($col) {
	var self = this;
	if(_ptsIsEditMode()) return;	// Not for edit mode unfortunatelly ....
	var $descCell = $col.find('.ptsColDesc');
	$col.height($col.height()); // Reset height on frontend.tables.js (listeners: window resize)
	$descCell.find('span').each(function(){
		var newFontSize = jQuery(this).data('new-font-size');
		if(!newFontSize) {
			var prevFontSize = jQuery(this).css('font-size')
			,	fontUnits = prevFontSize.replace(/\d+/, '')
			,	fontSize = parseInt(str_replace(prevFontSize, fontUnits, ''));
			if(fontSize && fontUnits) {
				newFontSize = Math.ceil(fontSize + (self._increaseHoverFontPerc * fontSize / 100));
				jQuery(this)
					.data('prev-font-size', prevFontSize)
					.data('font-units', fontUnits)
					.data('new-font-size', newFontSize);
			}
		}
		if(newFontSize) {
			jQuery(this).css('font-size', newFontSize+ jQuery(this).data('font-units'));
		}
	});

	if(this.getParam('is_horisontal_row_type') != '1') {

        if(!$descCell.attr('data-prev-height')){
        	var descHeight = $descCell.outerHeight();
            $descCell.attr('data-prev-height', descHeight);
		}else{
            descHeight =  $descCell.attr('data-prev-height');
		}
		$descCell.css({
			'min-height': descHeight
			,	'height': 'auto'
		});
	}

	$col.addClass('hover');
	if(_ptsIsEditMode()) {
		setTimeout(function(){
			var colElement = self.getElementByIterNum($col.data('iter-num'));
			if(colElement) {
				colElement.repositeMenu();
			}
		}, g_ptsHoverAnim);	// 300 - standard animation speed
	}
};
ptsBlock_price_table.prototype._backHoverFont = function($col) {
	if(_ptsIsEditMode()) return;	// Not for edit mode unfortunatelly ....
	$col.removeClass('hover');
	var $descCell = $col.find('.ptsColDesc');
	$descCell.find('span').each(function(){
		var prevFontSize = jQuery(this).data('prev-font-size');
		if(prevFontSize) {
			jQuery(this).css('font-size', prevFontSize);
		}
	});
	setTimeout(function(){
		$descCell.outerHeight( $descCell.data('prev-height') );
	}, 300);	// time is set in css styles, but it always is something around 300ms
};
ptsBlock_price_table.prototype._disableHoverEffect = function($cols) {
	this.setParam('enb_hover_animation', 0);
	if(_ptsIsEditMode()) return;	// Not for edit mode unfortunatelly ....
	$cols = $cols ? $cols : this._getCols();
	$cols.unbind('hover.animation');
};
ptsBlock_price_table.prototype.getColSelectors = function() {
	return {
			header: {sel: '.ptsColHeader'}
		,	desc: {sel: '.ptsColDesc'}
		,	rows: {sel: '.ptsRows'}
		,	cells: {sel: '.ptsCell'}
		,	footer: {sel: '.ptsColFooter'}
	};
};
ptsBlock_price_table.prototype.getMaxColsSizes = function( widthDesc ) {
	var $cols = this._getCols( widthDesc )
	,	sizes = this.getColSelectors();

	$cols.each(function(){
		//fix effects from bug with large min-height in frontend #266
		jQuery(this).css({'min-height' : 'auto'});
		for(var key in sizes) {
			if(key == 'rows') continue;
			var $entity = jQuery(this).find(sizes[ key ].sel);
			if($entity && $entity.size()) {
				if(key == 'cells') {
					if(!sizes[ key ].height)
						sizes[ key ].height = [];
					var cellNum = 0;
					$entity.each(function(){
						var prevHeight = jQuery(this).outerHeight();

						jQuery(this).css('height', 'auto');
						var height = jQuery(this).outerHeight();

						if(!sizes[ key ].height[ cellNum ] || sizes[ key ].height[ cellNum ] < height) {
							sizes[ key ].height[ cellNum ] = height;
						}
						//jQuery(this).outerHeight( prevHeight );
						cellNum++;
					});
				} else {
					var prevHeight = $entity.outerHeight();

					$entity.css('height', 'auto');
					var height = $entity.outerHeight();

					if(!sizes[ key ].height || sizes[ key ].height < height) {
						sizes[ key ].height = height;
					}
					$entity.outerHeight( prevHeight );
				}
			}
		}
	});
	return sizes;
};
ptsBlock_price_table.prototype.getColumnWithInfo = function(strWidthAttr) {
	var trimAttr = strWidthAttr.trim();
	var number = trimAttr.match('\\d+');
	var isPerc = trimAttr.match('%');
	var isPx = trimAttr.match('\\d+');
	if(number === null || (isPerc === null && isPx === null)) {
		return null;
	}
	return new Object({
		'num': (number.length && number.length > 0 && !isNaN(parseInt(number[0]))) ? parseInt(number[0]) : null,
		'isPerc': isPerc === null ? false : true
	});
};
ptsBlock_price_table.prototype.setColsWidth = function(width, perc) {
	var thatObj = this;
	if(thatObj.getParam('is_horisontal_row_type') != '1') {
		if (this.getParam('dsbl_responsive') === '1') {
			var tableWidth = this._$.width(),
				fixedValueTableWidth = this._$.width(),
				$cols = this._getCols(true),
				notSettedColumnWidthArr = new Array();
			$cols.each(function () {
				var col1 = jQuery(this);
				if (col1.length > 0 && col1[0].style && col1[0].style.width) {
					var colWidthObj = thatObj.getColumnWithInfo(col1[0].style.width);
					if (colWidthObj && 'num' in colWidthObj && 'isPerc' in colWidthObj) {
						var calcColWidth = 0;
						if (colWidthObj.isPerc) {
							calcColWidth = fixedValueTableWidth * colWidthObj.num / 100;
						} else {
							calcColWidth = colWidthObj.num;
						}
						var colPdL = parseFloat(col1.css('padding-left')),
							colPdR = parseFloat(col1.css('padding-right')),
							colMgL = parseFloat(col1.css('margin-left')),
							colMgR = parseFloat(col1.css('margin-right')),
							colSumMarginPadding = 0;
						if (!isNaN(colPdL)) {
							colSumMarginPadding += colPdL;
						}
						if (!isNaN(colPdR)) {
							colSumMarginPadding += colPdR;
						}
						if (!isNaN(colMgL)) {
							colSumMarginPadding += colMgL;
						}
						if (!isNaN(colMgR)) {
							colSumMarginPadding += colMgR;
						}
						tableWidth -= calcColWidth;
						calcColWidth = Math.floor(calcColWidth - colSumMarginPadding);
						if (calcColWidth < 0) {
							calcColWidth = 0;
						}
						col1.width(calcColWidth);
					} else {
						notSettedColumnWidthArr[notSettedColumnWidthArr.length] = col1;
					}
				} else {
					notSettedColumnWidthArr[notSettedColumnWidthArr.length] = col1;
				}
			});
			if (tableWidth > 0 && notSettedColumnWidthArr.length > 0) {
				var calcWidthForNsCol = Math.round(tableWidth / notSettedColumnWidthArr.length);
				for (var oneNsColumn in notSettedColumnWidthArr) {
					// set width for Other columns
					notSettedColumnWidthArr[oneNsColumn].width(calcWidthForNsCol);
				}
			}
		} else {
			width = parseFloat(width);
			if (width) {
				if (!perc) {
					this.setParam('col_width', width);
				}
				var $cols = this._getCols(true);
				if (perc) {
					width += '%';
				} else {
					width += 'px';
				}
				$cols.css({
					'width': width
				});
			}
		}
	} else {
		this.columnChidrensWidthCalc();
	}
};
ptsBlock_price_table.prototype.columnChidrensWidthCalc = function() {
	if(this.getParam('is_horisontal_row_type') != '1') {
		return;
	}
	// only for HORIZONTAL ROW
	var $cols = this._getCols(true);
	$cols.each(function (ind, oneCol) {
		var currColumn = jQuery(oneCol)
		,	emptyChilds = currColumn.find('.ptsTableElementContent').children(':not(:has(">div")):not(".ptsColBadge")')
		,	level1Childs = currColumn.find('.ptsTableElementContent').children(':has(">div"):not(".ptsColBadge")')
		,	level1ChildRows = currColumn.find('.ptsTableElementContent').children('.ptsRows')
		,	level2ChildRows = level1ChildRows.children()
		,	allChildCount = level1Childs.length
		,	level2ChildCount = 1;

		if(level1ChildRows.length > 0 && level2ChildRows.length > 0) {
			allChildCount += level2ChildRows.length -1;
			level2ChildCount = level2ChildRows.length;
		}
		//
		emptyChilds.css({
			'width': "0%",
			'margin': '0',
			'padding': '0',
		});
		var oneChildWidth = Math.floor(95 / allChildCount);

		level1Childs.css('width', oneChildWidth + '%');
		var widthInPixel = level1Childs.css('width');
		level1ChildRows.css('width', (level2ChildCount*oneChildWidth+2) + '%');
		level2ChildRows.css('width', widthInPixel);
	});
}
ptsBlock_price_table.prototype.checkColWidthPerc = function() {
	if(this.getParam('calc_width') === 'table') {
		this.setColWidthPerc();
	}
};
ptsBlock_price_table.prototype.setColWidthPerc = function() {
	var $cols = this._getCols( parseInt(this.getParam('enb_desc_col')) );
	this.setColsWidth( 100 / $cols.size(), true );
};
ptsBlock_price_table.prototype.setTableWidth = function(width, measure) {
	if(width && parseInt(width)) {
		width = parseInt(width);
		this.setParam('table_width', width);
	} else {
		width = this.getParam('table_width');
	}
	if(measure) {
		this.setParam('table_width_measure', measure);
	} else {
		measure = this.getParam('table_width_measure');
	}
	this._$.width( width+ measure );

};
ptsBlock_price_table.prototype.setTableVertPadding = function(padding, measure) {
	if(padding && parseInt(padding)) {
		padding = parseInt(padding);
		this.setParam('vert_padding', padding);
	} else {
		padding = this.getParam('vert_padding');
	}
	if(measure) {
		this.setParam('vert_padding_measure', measure);
	} else {
		measure = this.getParam('vert_padding_measure');
	}
	this._$.find('.ptsCol').css('paddingBottom', padding + 'px');
};
ptsBlock_price_table.prototype.setCalcWidth = function(type) {
	if(type) {
		this.setParam('calc_width', type);
	} else {
		type = this.getParam('calc_width');
	}
	switch(type) {
		case 'table':
			this.setTableWidth();
			this.setColWidthPerc();
			break;
		case 'col':
			var enb_desc_col = this.getParam('enb_desc_col')  != 0 ? true : false;
			this._$.width(this._getCols(enb_desc_col).size() * this.getParam('col_width') );
			this.setColsWidth( this.getParam('col_width') );
			break;
	}
};
ptsBlock_price_table.prototype._fixResponsive = function() {
	if(this.getParam('is_horisontal_row_type') == '1') {
		return;
	}
	var $parent = this._$.parents('.ptsTableFrontedShell:first').parent()
	,	parentWidth = $parent.width()
	,	widthMeasure = this.getParam('table_width_measure')
	,	calcWidth = this.getParam('calc_width')
	,	includeDesc = parseInt(this.getParam('enb_desc_col'))
	,	$cols = this._getCols( includeDesc )
	,	actualTblWidth = this._$.width()
	,	criticalColWidth = isNaN(parseInt(this.getParam('resp_min_col_width'))) ? 150 : parseInt(this.getParam('resp_min_col_width'))
	,	dsblResponsive = parseInt(this.getParam('dsbl_responsive'));
	this._$.removeClass('ptsBlockMobile');
	switch(calcWidth) {
		case 'table':
			switch(widthMeasure) {
				case '%':
					var self =  this
					,	removeOtherDescCol = function () {

						if (!dsblResponsive
							&& !_ptsIsEditMode()
							&& self._$.find('.ptsTableDescCol').size() > 1
							&& includeDesc)
						{
							var $descCols = self._$.find('.ptsTableDescCol')
							,	firstCol = false;

							$descCols.each(function () {
								var $this = jQuery(this);

								if (! firstCol) {
									firstCol = true;

									return;
								}

								$this.remove();
							});
						}
					};

					//removeOtherDescCol();

					$cols = this._getCols( includeDesc );

					var colsNum = $cols.size()
						,	currWidth = actualTblWidth / colsNum;
					if(currWidth <= criticalColWidth && !dsblResponsive) {
						$cols.css('width', '100%');

						if (!_ptsIsEditMode() && includeDesc) {
							var $descColumn = this._$.find('.ptsTableDescCol').first()
								,	$columns = this._$.find('.ptsCol:not(.ptsTableDescCol)')
								,	firstCol = false;
							removeOtherDescCol();

							var i = 0;
							$columns.each(function () {
								var $this = jQuery(this);

								if (! firstCol) {
									firstCol = true;
									return;
								}

								i++;
								$descColumn.clone().insertBefore($this);
							});

							this._isResponsiveDescInit = true;
							this._$.find('.ptsCol').css('width', '50%');
						}

						this._$.addClass('ptsBlockMobile');
						this.setParam('went_to_responsive', 1);
					} else {
						removeOtherDescCol();
						if(this.getParam('went_to_responsive')) {
							this.setColWidthPerc();
							this.setParam('went_to_responsive', 0);
						}
					}
					break;
				case 'px':
					if(actualTblWidth > parentWidth) {
						this.setParam('went_to_responsive', this.getParam('table_width'));
						this.setTableWidth(100, '%');	// make it 100% in this case
						this._fixResponsive();	// Repeat - to check case '%':
					} else if(this.getParam('went_to_responsive')) {
						this.setTableWidth(this.getParam('went_to_responsive'), 'px');	// Back to px width
						this.setParam('went_to_responsive', 0);
					}
					break;
			}
			break;
		case 'col':
			var colsNum = $cols.size()
			,	currWidth = parseFloat(this.getParam('col_width'));
			if(currWidth * colsNum >= parentWidth) {
				this.setParam('went_to_responsive', currWidth);
				this.setParam('table_width', 100);	// Set table width to 100%
				this.setParam('table_width_measure', '%');
				this.setCalcWidth('table');
				this._fixResponsive();	// Repeat - to check case '%':
			} else if(this.getParam('went_to_responsive')) {
				this.setCalcWidth('col');
				this.setParam('col_width', this.getParam('went_to_responsive'));	// Back to px width
				this.setParam('went_to_responsive', 0);
			}
			break;
	}
};
ptsBlock_price_table.prototype._refreshCellsHeight = function() {
	// Really important  - keep all this functionality as light as possible, as it can slow down whole builder work
	//console.time('_refreshCellsHeight');
	var $cols = this._getCols( true )
	,	self = this
	,	sizes = this.getMaxColsSizes( true );

	$cols.each(function(){
		for(var key in sizes) {

			var $entity = jQuery(this).find(sizes[ key ].sel);
			if(self.getParam('is_horisontal_row_type') == '1') {
				$entity.css({'height': 'auto'});
			} else {
				if(key == 'rows') {
					$entity.css({'height': 'auto'});
					continue;
				}
				if($entity && $entity.size()) {
					if(key == 'cells') {
						var cellNum = 0;
						$entity.each(function(){
							jQuery(this).css('height', sizes[ key ].height[ cellNum ] );
							cellNum++;
						});
					} else {
						$entity.outerHeight( sizes[ key ].height );

						if ($entity.outerHeight() != sizes[ key ].height) {
							$entity.css('height', sizes[ key ].height);
						}
					}
				}
			}
		}
	});
	//console.timeEnd('_refreshCellsHeight');
};