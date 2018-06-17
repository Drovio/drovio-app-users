var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	jq(document).on("team.show_step_buttons", function() {
		jq(".step.db .step-buttons").removeClass("noDisplay");
	});
});