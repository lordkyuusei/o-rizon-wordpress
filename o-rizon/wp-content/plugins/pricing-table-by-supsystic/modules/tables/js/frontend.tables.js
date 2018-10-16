var g_ptsEdit = false
,	g_ptsBlockFabric = null
,	g_ptsHoverAnim = 300	// Table hover animation lenght, ms - hardcoded for now
,	g_ptsHoverMargin = 20;	// Table hover margin displace, px - hardcoded for now

//array with unique_id template that without enabled options header row
//overlap toggle elements
var uniqueIdArray = ['wnOq3v2K', 'sa0DHT4h', 'B1gpCofW', 'ptmJ4YnA', 'r0OL4vCy'];

jQuery(document).ready(function(){
	_ptsInitFabric();
	if(typeof(ptsTables) !== 'undefined' && ptsTables && ptsTables.length) {
		for(var i = 0; i < ptsTables.length; i++) {
			g_ptsBlockFabric.addFromHtml(ptsTables[ i ], jQuery('#'+ ptsTables[ i ].view_id));
			jQuery('body').trigger('set_default_position');
			//for fix horizontal element size bugs frontend.pro.tables.js
			if(ptsTables[i].unique_id === "7m6k5X0i"){
				jQuery('#'+ ptsTables[ i ].view_id).attr('data-unique_id', ptsTables[i].unique_id);
			}

			// fix overlap toggle elements
            if(uniqueIdArray.indexOf(ptsTables[i].unique_id) != -1){
                jQuery('#'+ ptsTables[ i ].view_id).attr('data-unique_id', ptsTables[i].unique_id);
                //if undefined that means options is enabled by default.

                var enableHeader = '1';
				if (typeof ptsTables[i].params.enb_head_row !== 'undefined') {
                    enableHeader = typeof (ptsTables[i].params.enb_head_row.val) !== "undefined" ? '0' : '1';
                }
                jQuery(document.body).trigger( "overlay_toggle", [ ptsTables[i].unique_id , enableHeader] );
            }
			jQuery(".ptsTableFrontedShell .tooltipstered").removeAttr("title");
		}
	}
});
jQuery(window).load(function() {
	setTimeout(function() { jQuery('body').trigger('resize'); }, 500);
});
//in case images are loading dynamically
jQuery('.ptsEl.ptsCol[data-el="table_col"] img').on('load', function() {
	jQuery('body').trigger('resize');
});
function _ptsInitFabric() {
	g_ptsBlockFabric = new ptsBlockFabric();
}
function ptsGetFabric() {
	return g_ptsBlockFabric;
}
function _ptsIsEditMode() {
	return (typeof(g_ptsEditMode) !== 'undefined' && g_ptsEditMode);
}