var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Quick link
	jq(document).on("click", ".userDetails .close_button", function() {
		// Click on menu
		jq(this).trigger("dispose");
	});
	
	// Reload permissions
	jq(document).on("users.details.permissions.reload", function() {
		jq("#ref_permissions").trigger("reload");
	});
});