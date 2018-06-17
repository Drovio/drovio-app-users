var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	jq(document).on("pricing.show_step_buttons", function() {
		jq(".step.pricing .step-buttons").removeClass("noDisplay");
	});
});