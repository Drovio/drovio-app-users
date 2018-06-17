var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	jq(document).on("settings.show_step_buttons", function() {
		jq(".step.settings .step-buttons").removeClass("noDisplay");
	});
});