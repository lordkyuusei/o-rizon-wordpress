/**
 * Base block object - for extending
 * @param {object} blockData all block data from database (block database row)
 */
ptsBlockBase.prototype.destroy = function() {
	this._clearElements();
	this._$.slideUp(this._animationSpeed, jQuery.proxy(function(){
		this._$.remove();
		g_ptsBlockFabric.removeBlockByIter( this.getIter() );
		_ptsSaveCanvas();
	}, this));
};
ptsBlockBase.prototype.build = function(params) {
	params = params || {};
	var innerHtmlContent = '';
	if(this._data.css && this._data.css != '') {
		innerHtmlContent += '<style type="text/css" class="ptsBlockStyle">'+ this._data.css+ '</style>';
	}
	if(this._data.html && this._data.html != '') {
		innerHtmlContent += '<div class="ptsBlockContent">'+ this._data.html+ '</div>';
	}
	innerHtmlContent = '<div class="ptsBlock" id="{{block.view_id}}">'+ innerHtmlContent+ '</div>';
	if(!this._data.session_id) {
		this._data.session_id = mtRand(1, 999999);
	}
	if(!this._data.view_id) {
		this._data.view_id = 'ptsBlock_'+ this._data.session_id;
	}
	var template = twig({
		data: innerHtmlContent
	});
	var generatedHtml = template.render({
		block: this._data
	});
	this._$ = jQuery(generatedHtml);
	if(params.insertAfter) {
		this._$.insertAfter( params.insertAfter );
	}
	this._initElements();
	this._initHtml();
};
ptsBlockBase.prototype.set = function(key, value) {
	this._data[ key ] = value;
};
ptsBlockBase.prototype.setData = function(data) {
	this._data = data;
};
ptsBlockBase.prototype.getData = function() {
	return this._data;
};
ptsBlockBase.prototype.appendToCanvas = function() {
	this._$.appendTo('#ptsCanvas');
};
ptsBlockBase.prototype._initHtml = function() {
	this._beforeInitHtml();
};
ptsBlockBase.prototype._beforeInitHtml = function() {
	
};
ptsBlockBase.prototype._rebuildCss = function() {
	var template = twig({
		data: this._data.css
	});
	var generatedHtml = template.render({
		table: this._data
	});
	this.getStyle().html( generatedHtml );
};
ptsBlockBase.prototype.getStyle = function() {
	return this._$.find('style.ptsBlockStyle');
};
ptsBlockBase.prototype.setTaggedStyle = function(style, tag, elData) {
	this.removeTaggedStyle( tag );
	var $style = this.getStyle()
	,	styleHtml = $style.html()
	,	tags = this._getTaggedStyleStartEnd( tag );
	
	var template = twig({
		data: style
	});
	var generatedStyle = template.render({
		el: elData
	,	table: this._data
	}),	fullGeneratedStyleTag = tags.start+ "\n"+ generatedStyle+ "\n"+ tags.end;
	if (generatedStyle == undefined || !generatedStyle) return;
	$style.html(styleHtml+ fullGeneratedStyleTag);
	this.set('css', this.get('css')+ this._revertReplaceContent(fullGeneratedStyleTag));
};
ptsBlockBase.prototype.removeTaggedStyle = function(tag, params) {
	params = params || {};
	var tags = this._getTaggedStyleStartEnd(tag, true)
	,	$style = params.$style ? params.$style : this.getStyle()
	,	styleHtml = params.styleHtml ? params.styleHtml : $style.html()
	,	replaceRegExp = new RegExp(tags.start+ '(.|[\n\r])+'+ tags.end, 'gmi');
	$style.html( styleHtml.replace(replaceRegExp, '') );
	this.set('css', this.get('css').replace(replaceRegExp, ''));
};
ptsBlockBase.prototype.getTaggedStyle = function(tag) {
	// TODO: Finish this method
	var tags = typeof(tag) === 'string' ? this._getTaggedStyleStartEnd(tag) : tag;
};
ptsBlockBase.prototype._getTaggedStyleStartEnd = function(tag, forRegExp) {
	return {
		start: forRegExp ? '\\/\\*start for '+ tag+ '\\*\\/' : '/*start for '+ tag+ '*/'
	,	end: forRegExp ? '\\/\\*end for '+ tag+ '\\*\\/' : '/*end for '+ tag+ '*/'
	};
};
ptsBlockBase.prototype._initMenuItem = function(newMenuItemHtml, item) {
	if(this['_initMenuItem_'+ item.type] && typeof(this['_initMenuItem_'+ item.type]) === 'function') {
		var menuItemName = this.getParam('menu_item_name_'+ item.type);
		if(menuItemName && menuItemName != '') {
			newMenuItemHtml.find('.ptsBlockMenuElTitle').html( menuItemName );
		}
		this['_initMenuItem_'+ item.type]( newMenuItemHtml, item );
	}
};
ptsBlockBase.prototype._initMenuItem_align = function(newMenuItemHtml, item) {
	if(this._data.params && this._data.params.align) {
		//newMenuItemHtml.find('input[name="params[align]"]').val( this._data.params.align.val );
		//newMenuItemHtml.find('.ptsBlockMenuElElignBtn').removeClass('active');
		//newMenuItemHtml.find('.ptsBlockMenuElElignBtn[data-align="'+ this._data.params.align.val+ '"]').addClass('active');
		this._setAlign( this._data.params.align.val, true, newMenuItemHtml );
	}
	var self = this;
	newMenuItemHtml.find('.ptsBlockMenuElElignBtn').click(function(){
		self._setAlign( jQuery(this).data('align') );
	});
};
ptsBlockBase.prototype._clickMenuItem_align = function(options) {
	return false;
};
ptsBlockBase.prototype._setAlign = function( align, ignoreAutoSave, menuItemHtml ) {
	var possibleAligns = ['left', 'center', 'right'];
	for(var i in possibleAligns) {
		this._$.removeClass('ptsAlign_'+ possibleAligns[ i ]);
	}
	this._$.addClass('ptsAlign_'+ align);
	this.setParam('align', align);
	
	if(!menuItemHtml) {
		var menuOpt = this._$.data('_contentMenuOpt');
		menuItemHtml = menuOpt.items.align.$node;
	}
	menuItemHtml.find('input[name="params[align]"]').val( align );
	menuItemHtml.find('.ptsBlockMenuElElignBtn').removeClass('active');
	menuItemHtml.find('.ptsBlockMenuElElignBtn[data-align="'+ align+ '"]').addClass('active');
	
	if(!ignoreAutoSave) {
		_ptsSaveCanvas();
	}
};
ptsBlockBase.prototype._initMenuItem_bg_img = function(newMenuItemHtml, item) {
	if(this._data.params && this._data.params.bg_img_enb && parseInt(this._data.params.bg_img_enb.val)) {
		newMenuItemHtml.find('input[name="params[bg_img_enb]"]').attr('checked', 'checked');
	}
	var self = this;
	newMenuItemHtml.find('input[name="params[bg_img_enb]"]').change(function(){
		self.setParam('bg_img_enb', jQuery(this).attr('checked') ? 1 : 0);
		self._updateBgImg();
	});
};
ptsBlockBase.prototype._clickMenuItem_bg_img = function(options) {
	var self = this;
	ptsCallWpMedia({
		id: this._$.attr('id')
	,	clb: function(opts, attach, imgUrl) {
			// we will use full image url from attach.url always here (not image with selected size imgUrl) - as this is bg image
			// but if you see really big issue with this - just try to do it better - but don't broke everything:)
			self.setParam('bg_img', attach.url);
			self._updateBgImg();
		}
	});
};
ptsBlockBase.prototype._updateBgImg = function( ignoreAutoSave ) {
	this._rebuildCss();

	if(!ignoreAutoSave) {
		_ptsSaveCanvas();
	}
};
ptsBlockBase.prototype._clickMenuItem = function(key, options) {
	if(this['_clickMenuItem_'+ key] && typeof(this['_clickMenuItem_'+ key]) === 'function') {
		return this['_clickMenuItem_'+ key]( options );
	}
};
ptsBlockBase.prototype.getContent = function() {
	return this._$.find('.ptsBlockContent:first');
};
ptsBlockBase.prototype._revertReplaceContent = function(content) {
	var revertReplace = [
		{key: 'view_id'}
	];
	for(var i = 0; i < revertReplace.length; i++) {
		var key = revertReplace[ i ].key
		,	value = this.get( key )
		,	replaceFrom = [ value ]
		,	replaceTo = revertReplace[i].raw ? '{{table.'+ key+ '|raw}}' : '{{table.'+ key+ '}}';
		if(typeof(value) === 'string' && revertReplace[i].raw) {
			replaceFrom.push( value.replace(/\s+\/>/g, '>') );
		}
		for(var j = 0; j < replaceFrom.length; j++) {
			content = str_replace(content, replaceFrom[ j ], replaceTo);
		}
	}
	return content;
};
ptsBlockBase.prototype.getHtml = function() {
	var html = this.getContent().html();
	return this._revertReplaceContent( html );
};
ptsBlockBase.prototype.getCss = function() {
	var css = this.getStyle().html();
	return this._revertReplaceContent( css );
	return css;
};
ptsBlockBase.prototype.getIter = function() {
	return this._iter;
};
ptsBlockBase.prototype.beforeSave = function() {
	if(this._elements && this._elements.length) {
		for(var i = 0; i < this._elements.length; i++) {
			this._elements[ i ].beforeSave();
		}
	}
};
ptsBlockBase.prototype.afterSave = function() {
	if(this._elements && this._elements.length) {
		for(var i = 0; i < this._elements.length; i++) {
			this._elements[ i ].afterSave();
		}
	}
};
ptsBlockBase.prototype.mapElementsFromHtml = function($html, clb) {
	var self = this
	,	mapCall = function($el) {

		var element = self.getElementByIterNum( jQuery($el).data('iter-num') );
		if(element && element[ clb ]) {
			element[ clb ]();
		}
	};
	$html.find('.ptsEl').each(function(){
		mapCall( this );
	});
	if($html.hasClass('ptsEl')) {
		mapCall( $html );
	}
};
ptsBlockBase.prototype.replaceElement = function(element, toParamCode, type) {
	// Save current element content - in new element internal data
	var oldElContent = element.$().get(0).outerHTML
	,	oldElType = element.get('type')
	,	savedContent = element.$().data('pre-el-content');
	if(!savedContent)
		savedContent = {};
	savedContent[ oldElType ] = oldElContent;
	// Check if there are already saved prev. data for this type of element

	var newHtmlContent = '';

	if (type == 'btn') {
		var existsBtnHTML = null;

		for (var i in this._elements) {
			if (this._elements[i] instanceof ptsElement_btn && this._elements[i]._$.size()) {
				existsBtnHTML = this._elements[i]._$.get(0).outerHTML;

				break;
			}
		}

		if (existsBtnHTML)
			newHtmlContent = existsBtnHTML;
		else
			newHtmlContent = jQuery('#ptsElementButtonDefaultTemplate').removeAttr('id').get(0).outerHTML;
	} else {
		newHtmlContent = savedContent[ type ] ? savedContent[ type ] : this.getParam( toParamCode );
	}

	// Create and append new element HTML after current element
	var $newHtml = jQuery( newHtmlContent );
	$newHtml.insertAfter( element.$() );
	// Destroy current element
	var self = this;
	this.destroyElementByIterNum(element.getIterNum(), function(){
		self._disableContentChange = true;
		// Init new element after prev. one was removed
		var newElements = self._initElementsForArea( $newHtml );
		for(var i = 0; i < newElements.length; i++) {
			// Save prev. updated content info - in new elements $()
			newElements[ i ].$().data('pre-el-content', savedContent);
		}
		self._disableContentChange = false;
		self.contentChanged();
	});
};
ptsBlockBase.prototype.contentChanged = function() {
	if(!this._disableContentChange) {
		this._$.trigger('ptsBlockContentChanged', this);
	}
};
ptsBlockBase.prototype.hideElementsMenus = function( showEvent ) {
	if(this._elements && this._elements.length) {
		for(var i = 0; i < this._elements.length; i++) {
			if(this._elements[ i ].menuInAnimation()) return;	// Menu is in animation - so we don't need to hide it
			if(showEvent && showEvent != this._elements[ i ].getMenuShowEvent()) continue;
			this._elements[ i ].hideMenu();
		}
	}
};
/**
 * Price table block base class
 */
ptsBlock_price_table.prototype.addColumn = function() {
	var $colsWrap = this._getColsContainer()
	,	$cols = this._getCols()
	,	$col = null
	,	self = this;
	if($cols.size()) {
		var $lastCol = $cols.last();
		this.mapElementsFromHtml($lastCol, 'beforeSave');
		$col = $cols.last().clone();
		this.mapElementsFromHtml($lastCol, 'afterSave');
	} else {
		$col = jQuery( this.getParam('new_column_html') );
	}
	$colsWrap.append( $col );
	this._initElementsForArea( $col );
	this._initCellsEdit( $col.find('.ptsCell') );
	this._switchHeadRow({ $cols: $col });
	this._switchDescRow({ $cols: $col });
	this._switchFootRow({ $cols: $col });
	this._initCellsMovable( $col );
	this._refreshColNumbers();
	$cols = this._getCols();
	$cols.each(function(){
		var element = self.getElementByIterNum( jQuery(this).data('iter-num') );
		if(element) {
			// Update CSS style if required for updated classes
			element._setColor();
		}
	});
	this.checkColWidthPerc();
};
ptsBlock_price_table.prototype._initCellsEdit = function( $cell ) {
	var block = this;
    // #215
    //jQuery('.ptsColHeader').addClass('ptsCell');
	// #215
	//$cell = $cell ? $cell : this._$.find('.ptsCell, .ptsColHeader');

	$cell = $cell ? $cell : this._$.find('.ptsCell');
	$cell.each(function(){
		var $currentCell = jQuery(this);

		// Append cell buttons
		var $btnsShell = jQuery(this).find('.ptsCellEditBtnsShell');
		if($btnsShell.size()) {
			$btnsShell.html('');
		} else {
			$btnsShell = jQuery('<div class="ptsCellEditBtnsShell ptsShowSmooth" />').appendTo( this );
		}
		jQuery(this).on('click', function() {
			clearTimeout(jQuery(this).data('btn-shell-hide-timeout'));
			$btnsShell.addClass('active');

			var elementLeft = $btnsShell.offset().left
			,	elementWidth = $btnsShell.outerWidth()
			,	elementRight = elementLeft + elementWidth
			,	screenWidth  = screen.width;

			//off-screen element
			if(screenWidth < elementRight){
				$btnsShell.addClass('ptsRightArrows');
				$btnsShell.css({'left': -(elementWidth + 5) +'px'});
				$btnsShell.css({'left': -(elementWidth + 5) +'px'});
				if(!$('#ptsRightArrows').length){
					$('head').append('<style id="ptsRightArrows">.ptsCellEditBtnsShell.ptsRightArrows:before{border-left: 5px solid rgba(45, 34, 52, 0.96); border-right: none; left:'+elementWidth+'px}</style>');
				}
			}

			$btnsShell.find('.ptsTextAlignColumn .ptsTextAlignSwitch.active')
				.removeClass('active');

			switch ($currentCell.css('text-align')) {
				case 'left':
					$btnsShell.find('.ptsTextAlignColumn .ptsTextAlignSwitch[data-align="left"]')
						.addClass('active');
				break;
				case 'center':
					$btnsShell.find('.ptsTextAlignColumn .ptsTextAlignSwitch[data-align="center"]')
						.addClass('active');
				break;
				case 'right':
					$btnsShell.find('.ptsTextAlignColumn .ptsTextAlignSwitch[data-align="right"]')
						.addClass('active');
				break;
			}
		});
		jQuery(this).hover(function(){}, function() {
			$currentCell.data('btn-shell-hide-timeout', setTimeout(function(){
				$btnsShell.removeClass('active');
				$btnsShell.find('.ptsTooltipEditWnd').removeClass('active');
			}, 150));
		});
		// Move cell btn
		jQuery('#ptsMoveCellBtnExl').clone().removeAttr('id').appendTo( $btnsShell );
 
		jQuery('#ptsTextAlignColumn')
			.clone()
			.removeAttr('id')
			.appendTo( $btnsShell )
			.on('click', '.ptsTextAlignSwitch', function () {
				var $this = jQuery(this)
				,	$cell = jQuery(this).parents('.ptsCell:first')
				,	align = $this.attr('data-align');

				align = align.charAt(0).toUpperCase() + align.substr(1);

				$this.parent()
					.find('.ptsTextAlignSwitch')
					.removeClass('active');

				$this.addClass('active');

				$cell.removeClassWild('ptsCellAlign*');

				$cell.addClass('ptsCellAlign' + align);
			});

		// Add row after btn
		jQuery('#ptsAddRowAfterBtnExl').clone().removeAttr('id').appendTo( $btnsShell ).click(function(){
			block.addRow( jQuery(this).parents('.ptsCell:first').index(), true );
			return false;
		});
		// Add row before btn
		jQuery('#ptsAddRowBeforeBtnExl').clone().removeAttr('id').appendTo( $btnsShell ).click(function(){
			block.addRow( jQuery(this).parents('.ptsCell:first').index(), false );
			return false;
		});
		
		jQuery('#ptsAddOneCellInColumn').clone().removeAttr('id').appendTo( $btnsShell ).click(function(){
			var cell = jQuery(this).parents('.ptsCell:first').index()
			,	col = jQuery(this).closest('.ptsCol').index();
			
			block.addOneRow(cell, col, true);

			return false;
		});

		// Combining rows
		jQuery('#ptsCombiningPrevBtnExl').clone().removeAttr('id').appendTo( $btnsShell ).click(function(){
			var cell = jQuery(this).closest('.ptsCol')
									.find('.ptsRows .ptsCell')
									.get(
										jQuery(this)
											.parents('.ptsCell:first')
											.index()
			);

			block.combiningRow(cell);
			return false;
		});
		jQuery('#ptsCombiningNextBtnExl').clone().removeAttr('id').appendTo( $btnsShell ).click(function(){
			var cell = jQuery(this).closest('.ptsCol')
									.find('.ptsRows .ptsCell')
									.get(
										jQuery(this)
											.parents('.ptsCell:first')
											.index()
			);

			block.combiningRow(cell, true);
			return false;
		});

		// Tooltips edit buttons manipulations
		var $tooltipBtnShell = jQuery('#ptsTooltipEditBtnShellExl').clone().removeAttr('id').appendTo( $btnsShell );
		$tooltipBtnShell.find('.ptsTooltipEditBtn').click(function(){
			var $tooltipWnd = $tooltipBtnShell.find('.ptsTooltipEditWnd');
			if($tooltipWnd.hasClass('active')) {
				$tooltipWnd.removeClass('active') 
			} else {
				$tooltipWnd.find('[name=tooltip]').val( jQuery(this).parents('.ptsCell:first').attr('title') );
				$tooltipWnd.addClass('active');
			}
			return false;
		});
		$tooltipBtnShell.find('[name=tooltip]').change(function(){
			var tooltip = jQuery.trim( jQuery(this).val() );
			if(tooltip && tooltip != '') {
				jQuery(this).parents('.ptsCell:first').attr('title', tooltip);
			} else {
				jQuery(this).parents('.ptsCell:first').removeAttr('title');
			}
		});
		// Remove btn
		jQuery('#ptsRemoveRowBtnExl').clone().removeAttr('id').appendTo( $btnsShell ).click(function(){
			block.removeRow( jQuery(this).parents('.ptsCell:first') );
			return false;
		});
	});
};
ptsBlock_price_table.prototype._destroyCellsEdit = function( $cell ) {
	this._$.find('.ptsCellEditBtnsShell').remove();
};
ptsBlock_price_table.prototype.getColsNum = function() {
	return this._getCols().size();
};
ptsBlock_price_table.prototype.addPtsEl = function(parent) {
	var $cellAppend = jQuery(this.getParam('new_cell_html'));
	parent.before($cellAppend);
	this._disableContentChange = false;
	this._initElementsForArea($cellAppend);
	this._initCellsEdit($cellAppend);
	this._disableContentChange = false;
	this.contentChanged();
};
ptsBlock_price_table.prototype.addOneRow = function (positionCell, positionCol, isAfter) {
	var $cellAppend = jQuery(this.getParam('new_cell_html'))
	,	columnObject
	, 	$cellElement;

	for (var i in this._elements) {
		var elementObject = this._elements[i];

		if (elementObject instanceof ptsElement_table_col && elementObject._colNum == positionCol) {
			columnObject = elementObject;

			break;
		}
	}

	if (! columnObject) return;

	$cellElement = columnObject._$.find(this.getColSelectors().cells.sel).eq(positionCell);

	if (! $cellElement) return;

	this._disableContentChange = true;
	
	if (isAfter)
		$cellAppend.insertAfter($cellElement);
	else
		$cellAppend.insertBefore($cellElement);

	this._initElementsForArea($cellAppend);
	
	this._initCellsEdit($cellAppend);

	this._disableContentChange = false;

	this.contentChanged();
};
ptsBlock_price_table.prototype.addRow = function(positionIndex, after) {
	this._disableContentChange = true;
	var $cols = this._getCols( true )
	,	self = this;
	$cols.each(function(){
		var $rowsWrap = jQuery(this).find('.ptsRows')
		,	$cell = null;
		if(typeof(positionIndex) === 'undefined') {
			$cell = jQuery( self.getParam('new_cell_html') );
			$rowsWrap.append( $cell );
		} else {
			var $positionCell = $rowsWrap.find('.ptsCell:eq('+ positionIndex+ ')');
			self.mapElementsFromHtml($positionCell, 'beforeSave');
			$cell = $positionCell.clone();
			self.mapElementsFromHtml($positionCell, 'afterSave');
			after 
				? $positionCell.after( $cell )
				: $positionCell.before( $cell );
		}
		self._initElementsForArea( $cell );
		self._initCellsEdit( $cell );
	});
	this._disableContentChange = false;
	this.contentChanged();
};
ptsBlock_price_table.prototype.combiningRow = function (cell1, next) {
	var $cell1 = jQuery(cell1);
	var $cell2 = next ? jQuery($cell1.next('.ptsCell')) : jQuery($cell1.prev('.ptsCell'));

	if ($cell2.length == 0) return;

	this._disableContentChange = true;
	var c1, c2;

	if (next) {
		c1 = $cell1;
		c2 = $cell2;
	} else {
		c1 = $cell2;
		c2 = $cell1;
	}

	this.mapElementsFromHtml(c1, 'beforeSave');
	this.mapElementsFromHtml(c2, 'beforeSave');

	c1.find('.ptsCellEditBtnsShell').remove();
	c2.find('.ptsCellEditBtnsShell').remove();
	c1.html(c1.html() + c2.html());
	c2.remove();
	
	this.mapElementsFromHtml(c1, 'afterSave');
	this.mapElementsFromHtml(c2, 'afterSave');

	this._initElementsForArea( c1 );
	this._initCellsEdit( c1 );

	this._disableContentChange = false;
	this.contentChanged();
};
ptsBlock_price_table.prototype.removeRow = function( $cell ) {
	var block = this
	,	cellIndex = $cell && typeof($cell) === 'object' ? $cell.index() : false
	,	$cols = this._getCols( true );
	if(cellIndex === false) {
		cellIndex = typeof($cell) === 'number' ? $cell : $cols.last().find('.ptsCell').size() - 1;
	}
	if(block._data && block._data.params && block._data.params.is_horisontal_row_type && block._data.params.is_horisontal_row_type.val && block._data.params.is_horisontal_row_type.val == 1) {
		setTimeout(function(){
			$cell.animateRemovePts( g_ptsAnimationSpeed );
		}, g_ptsAnimationSpeed);
	} else {
		$cols.each(function(){
			var $rowsWrap = jQuery(this).find('.ptsRows')
				,	$removeCell = $rowsWrap.find('.ptsCell:eq('+ cellIndex+ ')');
			if($removeCell && $removeCell.size()) {
				var $elements = $removeCell.find('.ptsEl');
				$elements.each(function(){
					block.removeElementByIterNum( jQuery(this).data('iter-num') );
				});
				setTimeout(function(){
					$removeCell.animateRemovePts( g_ptsAnimationSpeed );
				}, g_ptsAnimationSpeed);	// Wait animation speed time to finally remove cell html element
			}
		});
	}
	setTimeout(function(){
		block.contentChanged();
	}, 2 * g_ptsAnimationSpeed + 50);	// See prev lines - timeout for g_ptsAnimationSpeed + animation remove for same time g_ptsAnimationSpeed
};
ptsBlock_price_table.prototype.removeCol = function( $col ) {
	var $cols = this._getCols();
	if($cols.size()) {
		var $removeCol = null;
		if(typeof($col) === 'object') {	// Colum jquery obj specified
			$removeCol = $col;
		} else if(typeof($col) === 'number') {	// Column item number specified
			$removeCol = $cols.filter(':eq('+ $col+ ')');
		} else {	// Nothing was specified - remove last column in set
			$removeCol = $cols.last();
		}
		var colElement = this.getElementByIterNum( $removeCol.data('iter-num') );
		if(colElement) {
			var self = this;
			colElement.destroy(function(){
				self.contentChanged();
			});
		}
	}
};
ptsBlock_price_table.prototype.getRowsNum = function() {
	return this._getCols().first().find('.ptsRows').find('.ptsCell').size();
};
ptsBlock_price_table.prototype._initHtml = function() {
	ptsBlock_price_table.superclass._initHtml.apply(this, arguments);
	var $colsWrap = this._getColsContainer()
	,	self = this
	,   original_id = self._data.original_id
	,   axis = 'x';
	
	if(original_id == '39'){
		axis = 'y';
	}
	
	$colsWrap.sortable({
		items: '.ptsCol:not(.ptsTableDescCol)'
	,	axis: axis
	,	handle: '.ptsMoveHandler'
	,	start: function(e, ui) {
			_ptsSetSortInProgress( true );
			var dragElement = self.getElementByIterNum( ui.item.data('iter-num') );
			if(dragElement) {
				dragElement.onSortStart();
			}
		}
	,	stop: function(e, ui) {
			_ptsSetSortInProgress( false );
			var dragElement = self.getElementByIterNum( ui.item.data('iter-num') );
			if(dragElement) {
				dragElement.onSortStop();
			}

			var desiredOrder = [],
				unsortedCols = [];

			var $cols = self._getCols(),
				num = 1;

			$cols.each(function(){
				var element = self.getElementByIterNum( jQuery(this).data('iter-num') );
				
				if(element) {
					if (element._colNum != num) {
						desiredOrder.push(num);
						unsortedCols.push(element);
					}
					var classes = jQuery(this).attr('class')
					,	newClasses = '';
					newClasses = (classes.replace(/ptsCol\-\d+/g, '')+ ' ptsCol-'+ num).replace(/\s+/g, ' ');
					jQuery(this).attr('class', newClasses);
				}

				num++;
			});

			var blockCss = self.get('css'),
				resultCss = '';
			for (var i = 0; i < unsortedCols.length; i++) {
				var el = unsortedCols[i],
					newNum = desiredOrder[i],
					num = unsortedCols[i]._colNum,
					mark = self._getTaggedStyleStartEnd('col color ' + unsortedCols[i]._colNum),
					colCss = '';

				el._setColNum( newNum );

				if (blockCss.indexOf(mark.start) > -1 && blockCss.indexOf(mark.end) > -1) {
					colCss = blockCss.substring(blockCss.indexOf(mark.start), blockCss.indexOf(mark.end) + mark.end.length);
				}

				if (! colCss.length)
					continue;
				var s = mark.start.replace('col color ' + num, 'col color ' + newNum);
				var e = mark.end.replace('col color ' + num, 'col color ' + newNum);

				colCss = colCss.replace(mark.start, s);
				colCss = colCss.replace(mark.end, e);
				colCss = str_replace_all(colCss, '.ptsCol-' + num, '.ptsCol-' + newNum);
				self.removeTaggedStyle('col color ' + num);
				resultCss += colCss;
			}
			if (!resultCss.length) return;
			self.set('css', self.get('css') + resultCss);
			self._rebuildCss();
			self.contentChanged();
		}
	});
	// Set cols numbers for all columns

	this._refreshColNumbers();
	this._initCellsEdit();
	this._initCellsMovable();
};
ptsBlock_price_table.prototype._refreshColNumbers = function() {
	var	self = this
	,	$cols = this._getCols()
	,	num = 1;
	$cols.each(function(){
		var element = self.getElementByIterNum( jQuery(this).data('iter-num') );
		if(element) {
			element._setColNum( num );
			var classes = jQuery(this).attr('class')
			,	newClasses = '';
			newClasses = (classes.replace(/ptsCol\-\d+/g, '')+ ' ptsCol-'+ num).replace(/\s+/g, ' ');
			jQuery(this).attr('class', newClasses);
		}
		num++;
	});
};
ptsBlock_price_table.prototype._setColorFromColorpicker = function( pcColor, ignoreAutoSave ) {
	this.setParam('bg_color', pcColor.formatted);
	this._updateFillColor( ignoreAutoSave );
};
ptsBlock_price_table.prototype._updateFillColor = function( ignoreAutoSave ) {
	this._rebuildCss();
	if(!ignoreAutoSave) {
		_ptsSaveCanvas();
	}
};
ptsBlock_price_table.prototype._getDescCol = function() {
	return this._$.find('.ptsTableDescCol');
};
ptsBlock_price_table.prototype.switchDescCol = function(state) {
	var $descCol = this._getDescCol();
	this.setParam('enb_desc_col', state ? 1 : 0);
	state 
		? $descCol.show()
		: $descCol.hide();
	this.checkColWidthPerc();
};
ptsBlock_price_table.prototype._switchHeadRow = function(params) {
	params = params || {};
	if(typeof(params.state) === 'undefined') {
		params.state = !parseInt(this.getParam('hide_head_row'));	// "!" here is because option is actually for hide
	} else {
		this.setParam('hide_head_row', params.state ? 0 : 1);
	}
	if(typeof(params.$cols) === 'undefined') {
		params.$cols = this._getCols( true );
	}
	params.$cols.each(function(){
		var $cell = jQuery(this).find('.ptsColHeader');
		if($cell && $cell.size()) {
			params.state 
				? $cell.show()
				: $cell.hide();
		}
	});
};
ptsBlock_price_table.prototype._switchDescRow = function(params) {
	params = params || {};
	if(typeof(params.state) === 'undefined') {
		params.state = ! parseInt(this.getParam('hide_desc_row'));	// "!" here is because option is actually for hide
	} else {
		this.setParam('hide_desc_row', params.state ? 0 : 1);
	}
	if(typeof(params.$cols) === 'undefined') {
		params.$cols = this._getCols( true );
	}
	params.$cols.each(function(){
		var $cell = jQuery(this).find('.ptsColDesc');
		if($cell && $cell.size()) {
			params.state 
				? $cell.show()
				: $cell.hide();
		}
	});
};
ptsBlock_price_table.prototype._switchFootRow = function(params) {
	params = params || {};
	if(typeof(params.state) === 'undefined') {
		params.state = !parseInt(this.getParam('hide_foot_row'));	// "!" here is because option is actually for hide
	} else {
		this.setParam('hide_foot_row', params.state ? 0 : 1);
	}
	if(typeof(params.$cols) === 'undefined') {
		params.$cols = this._getCols( true );
	}
	params.$cols.each(function(){
		var $cell = jQuery(this).find('.ptsColFooter');
		if($cell && $cell.size()) {
			params.state 
				? $cell.show()
				: $cell.hide();
		}
	});
};
ptsBlock_price_table.prototype.beforeSave = function() {
	ptsBlock_price_table.superclass.beforeSave.apply(this, arguments);
	var $hoveredCol = this._getCols().filter('.hover');
	if($hoveredCol && $hoveredCol.size()) {
		this._backHoverFont( $hoveredCol );
		this._$lastHoveredCol = $hoveredCol;
	}
	this._destroyCellsEdit();
};
ptsBlock_price_table.prototype.afterSave = function() {
	ptsBlock_price_table.superclass.afterSave.apply(this, arguments);
	if(this._$lastHoveredCol) {
		this._increaseHoverFont( this._$lastHoveredCol );
		this._$lastHoveredCol = null;
	}
	this._initCellsEdit();
};
ptsBlock_price_table.prototype._initCellsMovable = function($cols) {
	$cols = $cols ? $cols : this._getCols( true );
	var block = this;
	$cols.each(function(){
		jQuery(this).find('.ptsRows').sortable({
			items: '.ptsCell'
		,	axis: 'y'
		,	handle: '.ptsMoveCellBtn'
		// No placeholder for now - it is look nice now without it too
		//,	placeholder: 'ptsCellDragHolder'
		,	stop: function(event, ui) {
				block._refreshCellsHeight();
			}
		});
	});
};
