var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Refresh key list
	jq(document).on("settings.keys.list.reload", function() {
		jq("#ref_keys").trigger("reload");
	});
});