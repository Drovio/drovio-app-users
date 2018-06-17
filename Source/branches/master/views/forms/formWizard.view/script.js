var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Click on step header
	jq(document).on("click", ".formWizard .step .step__title", function() {
		// Return if already open
		if (jq(this).closest(".step").hasClass("selected"))
			return;
		
		// De-select all steps
		jq(".formWizard .step").removeClass("selected");
		
		// Select current step
		jq(this).closest(".step").addClass("selected").find(".step__body").animate({
			height: "show"
		}, 200);
		
		// Hide all the rest
		jq(".formWizard .step:not(.selected) .step__body").animate({
			height: "hide"
		}, 200);
	});
	
	// Continue buttons for every step
	jq(document).on("click", ".formWizard .step.html_sample .wbutton.continue", function() {
		jq(".formWizard .step.api .step__title").trigger("click");
	});
	
	// Continue buttons for every step
	jq(document).on("click", ".formWizard .step.api .wbutton.continue", function() {
		// Select the next step
		jq(".formWizard .step.social .step__title").trigger("click");
	});
	
	// Next buttons for every step
	jq(document).on("click", ".formWizard .step.final .wbutton.next_form", function() {
		// Select the next step
		jq(".formExplorer .wz-menu .wz-menu-item.selected").next().trigger("click");
	});
	
	/*
	// Continue buttons for every step
	jq(document).on("click", ".formWizard .step.embed .step__title", function() {
		// Get code from editors and combine
		var codeMirrorInstance = jq(".formWizard .step.overview .html5editor_cm").data("CodeMirrorInstance");
		var frontendCode = codeMirrorInstance.getDoc().getValue();
		
		var selectedBackend = jq(".formWizard .step.api input[name='lbackend']:checked").val();
		var codeMirrorInstance = jq(".formWizard .step.api textarea[name='backend_"+selectedBackend+"']").closest(".cmEditor").data("CodeMirrorInstance");
		var backendCode = codeMirrorInstance.getDoc().getValue();
		
		// Set final code
		var codeMirrorInstance = jq(".formWizard .step.embed .cmEditor_embed").data("CodeMirrorInstance");
		codeMirrorInstance.getDoc().setValue(backendCode + "\n" + frontendCode);
	});*/
	
	
	// Load zeroclipboard
	jq.getScript("//cdn.drov.io/libs/zeroclipboard/ZeroClipboard.min.js", function() {
		// Add listener to activate copy button
		jq(document).on("formwizard.activate_copy", function() {
			// Enable code for copying the html code ;-)
			var htmlClient = new ZeroClipboard(document.getElementById("copy-html-code"));
			htmlClient.on("ready", function(readyEvent) {
				// ZeroClipboard SWF is ready!
				htmlClient.on( "aftercopy", function(event) {
					// Show notification label
					var jqLabel = jq(".formWizard .step.html_sample .copy-html-label");
					jqLabel.show().css("opacity", 1);
					
					// Set timeout to hide after 5 seconds
					setTimeout(function() {
						jqLabel.animate({
							opacity: 0
						}, 1000, function() {
							jq(this).hide();
						});
					}, 2000);
				});
			});
			
			
			// Enable code for copying the backend code ;-)
			jq(".formWizard .step.api .languageContainer .lItem").each(function() {
				var lname = jq(this).data("lng");
				var htmlClient = new ZeroClipboard(document.getElementById("copy-api-"+lname));
				htmlClient.on("ready", function(readyEvent) {
					// ZeroClipboard SWF is ready!
					htmlClient.on( "aftercopy", function(event) {
						// Show notification label
						var jqLabel = jq(".formWizard .step.api .editorContainer:not(.noDisplay) .copy-api-label");
						jqLabel.show().css("opacity", 1);

						// Set timeout to hide after 5 seconds
						setTimeout(function() {
							jqLabel.animate({
								opacity: 0
							}, 1000, function() {
								jq(this).hide();
							});
						}, 2000);
					});
				});
			});
		});
	});
});