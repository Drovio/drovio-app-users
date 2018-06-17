var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Close popup
	jq(document).on("click", ".demoPopup .close_button", function() {
		jq(this).trigger("dispose");
	});
});