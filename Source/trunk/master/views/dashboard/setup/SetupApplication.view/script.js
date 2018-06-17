var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Next step
	jq(document).on("click", ".setupApplication .step-buttons .step-btn.next", function() {
		// Check if there is a next step
		var stepCount = jq(".step").length;
		var currentStep = jq(".step.active").index();
		if (currentStep >= stepCount)
			return;
		
		// Hide current step
		jq(".step.active").animate({
			opacity: "hide"
		}, "300", function() {
			// Show next step
			jq(this).removeClass("active").css("display", "");
			jq(".step").eq(currentStep).addClass("active");
			
			// Refresh step counter
			jq(document).trigger("step-counter.refresh");
		});
	});
	
	// Show page notifications
	jq(document).on("app.notification", function(ev, notification) {
		pageNotification.show(jq(document), "application-notification", notification, "", null, null, true);
	});
	
	// Refresh step counter listener
	jq(document).on("step-counter.refresh", function() {
		// Set current step
		jq(".step-counter b.step_current").html(jq(".step.active").index());
		
		// Set total steps
		jq(".step-counter b.step_total").html(jq(".step").length);
	});
	
	// Refresh step counter
	jq(document).trigger("step-counter.refresh");
});