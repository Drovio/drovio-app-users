var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Refresh key list
	jq(document).on("settings.keys.list.reload", function() {
		jq("#ref_keys").trigger("reload");
	});
	
	// Refresh permissions feature
	jq(document).on("settings.permissions.reload", function() {
		// Show notification
		pageNotification.show(jq(document), "settings.permissions.notification", "Permissions activated.", "", null, null, true);
		
		// Reload permissionsm
		jq("#ref_permissions").trigger("reload");
	});
});