var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	jq(document).on("authSettings.show_step_buttons", function() {
		jq(".step.auth .step-buttons").removeClass("noDisplay");
	});
});