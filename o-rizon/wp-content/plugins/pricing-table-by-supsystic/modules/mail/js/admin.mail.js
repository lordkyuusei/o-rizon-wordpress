jQuery(document).ready(function(){
	jQuery('#ptsMailTestForm').submit(function(){
		jQuery(this).sendFormPts({
			btn: jQuery(this).find('button:first')
		,	onSuccess: function(res) {
				if(!res.error) {
					jQuery('#ptsMailTestForm').slideUp( 300 );
					jQuery('#ptsMailTestResShell').slideDown( 300 );
				}
			}
		});
		return false;
	});
	jQuery('.ptsMailTestResBtn').click(function(){
		var result = parseInt(jQuery(this).data('res'));
		jQuery.sendFormPts({
			btn: this
		,	data: {mod: 'mail', action: 'saveMailTestRes', result: result}
		,	onSuccess: function(res) {
				if(!res.error) {
					jQuery('#ptsMailTestResShell').slideUp( 300 );
					jQuery('#'+ (result ? 'ptsMailTestResSuccess' : 'ptsMailTestResFail')).slideDown( 300 );
				}
			}
		});
		return false;
	});
	jQuery('#ptsMailSettingsForm').submit(function(){
		jQuery(this).sendFormPts({
			btn: jQuery(this).find('button:first')
		});
		return false; 
	});
});