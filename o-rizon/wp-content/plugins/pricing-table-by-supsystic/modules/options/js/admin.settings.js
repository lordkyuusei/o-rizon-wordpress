jQuery(document).ready(function(){
	jQuery('#ptsSettingsSaveBtn').click(function(){
		jQuery('#ptsSettingsForm').submit();
		return false;
	});
	jQuery('#ptsSettingsForm').submit(function(){
		jQuery(this).sendFormPts({
			btn: jQuery('#ptsSettingsSaveBtn')
		});
		return false;
	});
});