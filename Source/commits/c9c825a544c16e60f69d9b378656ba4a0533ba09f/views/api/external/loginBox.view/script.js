var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Close dialog
	jq(document).on("click", ".identity-login-box.dialog .box-header .btn-close", function() {
		// Click on menu
		jq(this).trigger("dispose");
		jq(this).trigger("login-box-dispose");
		jq(this).closest(".identity-login-box-container").detach();
	});
	
	// Switch forms
	jq(document).on("click", ".identity-login-box .box-footer .ft-lnk", function() {
		// Get form reference
		var fref = jq(this).data("fref");
		
		// Hide all forms and show the selected one
		jq(".box-main").addClass("hidden");
		jq(".box-main." + fref).removeClass("hidden");
		
		// Adjust footer links
		jq(".ft-lnk, .ft-lnk-bull").removeClass("hidden");
		jq(this).addClass("hidden");
		jq(".ft-lnk-bull." + fref).addClass("hidden");
	});
	
	// Reset and recover forms
	jq(document).on("click", ".identity-login-box .box-main .bx-sub.action.reset", function() {
		// Hide all forms and show the selected one
		jq(".box-main").addClass("hidden");
		jq(".box-main.reset").removeClass("hidden");
	});
	
	// Reset and recover forms
	jq(document).on("click", ".identity-login-box .box-main .bx-sub.action.recover", function() {
		// Hide all forms and show the selected one
		jq(".box-main").addClass("hidden");
		jq(".box-main.recover").removeClass("hidden");
	});
	
	// Submit form
	jq(document).on('submit', '.identity-login-box form[data-async]', function(ev) {
		// Stops the Default Action (if any)
		ev.preventDefault();

		// Submit form
		loginBox.submitForm(ev, jq(this), function(ev, response) {
			// Get response
			console.log(response);
		});
	});
});