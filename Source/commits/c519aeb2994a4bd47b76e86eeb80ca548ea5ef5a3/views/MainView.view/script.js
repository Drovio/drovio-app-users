var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Load application containers
	jq(document).on("click", ".userManagement .sidebar .menuitem", function() {
		// Get ref and load view
		var ref = jq(this).data("ref");
		jq("#" + ref).trigger("load");
	});
	
	// Re-Load application containers
	jq(document).on("click", ".userManagement .sidebar .menuitem .reload", function() {
		// Get ref and load view
		var ref = jq(this).closest(".menuitem").data("ref");
		jq("#" + ref).trigger("reload");
	});
	
	// Close welcome notification
	jq(document).on("click", ".userManagement .welcome_notification .close_button", function() {
		// remove notification
		jq(this).closest(".welcome_notification").detach();
	});
});