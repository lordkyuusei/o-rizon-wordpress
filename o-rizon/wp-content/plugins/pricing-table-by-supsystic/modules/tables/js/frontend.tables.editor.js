var g_ptsMainMenu = null
,	g_ptsFileFrame = null	// File frame for wp media uploader
,	g_ptsEdit = true
,	g_ptsTopBarH = 32		// Height of the Top Editor Bar
,	g_ptsSortInProgress = false
,	g_ptsEditMode = true	// If this script is loaded - this mean that we in edit mode
,	g_ptsVandColorPickerOptions = {
		'altAlpha': false,
		'showOn': 'alt',
		'altProperties': 'background-color',
		'altColorFormat': 'rgba(rd,gd,bd,af)',
		'okOnEnter': true,
		//'mode': 's',
		'alpha': true,
		'color': 'rgba(255, 255, 255, 0.8)',
		'colorFormat': 'RGBA',
		'title': 'Pick a color',
		'part': { 'map': { size: 128 }, 'bar': { size: 128 } },
		'parts': [ 'map', 'bar', 'rgb', 'alpha', 'hex', 'preview' ],
		'layout': {'map':		[0, 0, 1, 4],'bar':		[1, 0, 1, 4],'preview':	[2, 0, 1, 1],'rgb':		[2, 1, 1, 1],'alpha':  	[2, 2, 1, 1],'hex':		[2, 3, 1, 1],},
};
jQuery(document).ready(function(){
	_ptsInitTwig();
	// Prevent all default browser event - such as links redirecting, forms submit, etc.
	jQuery('#ptsCanvas').on('click', 'a', function(event){
		event.preventDefault();
	});
	jQuery('.ptsMainSaveBtn').click(function(){
		_ptsSaveCanvas();
		return false;
	});
});
function _ptsSaveCanvasDelay(delay) {
	delay = delay ? delay : 200;
	setTimeout(_ptsSaveCanvas, delay);
}
function _ptsSaveCanvas(params, byHands) {
	if(!!parseInt(toeOptionPts('disable_autosave')) && 'undefined' == typeof byHands) {
		return;	// Autosave disabled in admin area
	}
	if(typeof(ptsTables) === 'undefined' || !ptsTables || !ptsTables.length || (typeof(g_ptsIsTableBuilder) !== 'undefined' && g_ptsIsTableBuilder)) {
		return;
	}
	if(typeof(ptsTables[0].params.enable_switch_toggle) === 'undefined'){
		//toggle options not enabled
	} else {
		//toggle enabled
		jQuery(document.body).trigger('updateToggleHtml');
	}
	
	params = params || {};
	var dataForSave = {
		mod: 'tables'
	,	action: 'save'
	,	data: ptsGetFabric().getDataForSave()[0]	//[0] - is because only one block (table) is in this plugin saved
	};
	if(params.sendData) {
		for(var key in params.sendData) {
			dataForSave.data[ key ] = params.sendData[ key ];
		}
	}
	jQuery.sendFormPts({
		btn: jQuery('.ptsTableSaveBtn')
	,	data: dataForSave
	,	onSuccess: function(res){
			if(!res.error) {
				
			}
		}
	});
}
function _ptsSortInProgress() {
	return g_ptsSortInProgress;
}
function _ptsSetSortInProgress(state) {
	g_ptsSortInProgress = state;
}
function _ptsInitTwig() {
	Twig.extendFunction('adjBs', function(hex, steps) {
		if(!hex)
			return hex;
		var isRgb = hex.indexOf('rgb') !== -1;
		if(isRgb) {
			var colorObj = tinycolor( hex );
			hex = colorObj.toHex();
		}
		// Steps should be between -255 and 255. Negative = darker, positive = lighter
		steps = Math.max(-255, Math.min(255, steps));
		// Normalize into a six character long hex string
		hex = str_replace(hex, '#', '');
		if (hex.length == 3) {
			hex = str_repeat(hex.substr(0, 1), 2)+ str_repeat(hex.substr(1, 1), 2)+ str_repeat(hex.substr(2, 1), 2);
		}
		// Split into three parts: R, G and B
		var color_parts = str_split(hex, 2);
		var res = '#';
		for(var i in color_parts) {
			var color = color_parts[ i ];
			color   = hexdec(color); // Convert to decimal
			color   = Math.max(0, Math.min(255, color + steps)); // Adjust color
			res += str_pad(dechex(color), 2, '0', 'STR_PAD_LEFT'); // Make two char hex code
		}
		if(isRgb) {
			return tinycolor( res ).setAlpha( colorObj.getAlpha() );
		}
		return res;
	});
}