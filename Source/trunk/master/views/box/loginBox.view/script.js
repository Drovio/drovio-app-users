var jq = jQuery.noConflict();
// loginBox listeners
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

	// Reset all forms
	jq(".identity-login-box form").each(function() {
		loginBox.resetForm(jq(this));
		loginBox.clearFormReport(jq(this));
	});

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

// Login form
jq(document).on('submit', '.identity-login-box .box-main.login form[data-async]', function(ev) {
	// Stops the Default Action (if any)
	ev.preventDefault();

	// Submit form
	loginBox.submitForm(ev, jq(this), function(ev, response) {
		// Check if there is an error
		if (typeof response.body['error'] != "undefined") {
			// Show message
			loginBox.setFormErrorReport(jq(this), response.body.error.payload.message);
		} else if (typeof response.body['login'] != "undefined") {
			// Get status and set report message
			var status = response.body.login.payload.status;
			if (status == 1 && response.body.login.payload.auth_token != undefined) {
				// Set auth token
				loginBox.setAuthToken(response.body.login.payload.auth_token);

				// Show success
				jq(".box-main").addClass("hidden");
				jq(".box-main.success").removeClass("hidden");
				jq(".box-main.success .bx-succ-title").html(response.body.login.payload.message);

				// Login callback
				if (typeof loginBox.options.login_callback == 'function') {
					setTimeout(function() {
						loginBox.options.login_callback.call();
					}, 2000);
				}
			} else
				loginBox.setFormErrorReport(jq(this), response.body.login.payload.message);
		}

		// Reset form
		loginBox.resetForm(jq(this));
	});
});

// Register form
jq(document).on('submit', '.identity-login-box .box-main.register form[data-async]', function(ev) {
	// Stops the Default Action (if any)
	ev.preventDefault();

	// Submit form
	loginBox.submitForm(ev, jq(this), function(ev, response) {
		// Check if there is an error
		if (typeof response.body['error'] != "undefined") {
			// Show message
			loginBox.setFormErrorReport(jq(this), response.body.error.payload.message);
		} else if (typeof response.body['register'] != "undefined") {
			// Get status and set report message
			var status = response.body.register.payload.status;
			if (status == 1) {
				// Check and set auth_token
				if (response.body.register.payload.auth_token != undefined) {
					// Set auth token
					loginBox.setAuthToken(response.body.register.payload.auth_token);
				}

				// Show success
				jq(".box-main").addClass("hidden");
				jq(".box-main.success").removeClass("hidden");
				jq(".box-main.success .bx-succ-title").html(response.body.register.payload.message);

				// Register callback
				if (typeof loginBox.options.register_callback == 'function') {
					setTimeout(function() {
						loginBox.options.register_callback.call();
					}, 2000);
				}
			} else
				loginBox.setFormErrorReport(jq(this), response.body.register.payload.message);

			// Reset form
			loginBox.resetForm(jq(this));
		}
	});
});

// Recovery form
jq(document).on('submit', '.identity-login-box .box-main.recover form[data-async]', function(ev) {
	// Stops the Default Action (if any)
	ev.preventDefault();

	// Submit form
	loginBox.submitForm(ev, jq(this), function(ev, response) {
		// Check if there is an error
		if (typeof response.body['error'] != "undefined") {
			// Show message
			loginBox.setFormErrorReport(jq(this), response.body.error.payload.message);
		} else if (typeof response.body['recover'] != "undefined") {
			// Get status and set report message
			var status = response.body.recover.payload.status;
			if (status == 1) {
				// Report, set cookie and callback
				loginBox.setFormReport(jq(this), response.body.recover.payload.message);

				// Show success
				jq(".box-main").addClass("hidden");
				jq(".box-main.success").removeClass("hidden");
				jq(".box-main.success .bx-succ-title").html(response.body.recover.payload.message);

				// Hide all forms and show the selected one
				setTimeout(function() {
					jq(".box-main").addClass("hidden");
					jq(".box-main.reset").removeClass("hidden");
				}, 2500);
			} else
				loginBox.setFormErrorReport(jq(this), response.body.recover.payload.message);
		}

		// Reset form
		loginBox.resetForm(jq(this));
	});
});

// Reset form
jq(document).on('submit', '.identity-login-box .box-main.reset form[data-async]', function(ev) {
	// Stops the Default Action (if any)
	ev.preventDefault();

	// Submit form
	loginBox.submitForm(ev, jq(this), function(ev, response) {
		// Check if there is an error
		if (typeof response.body['error'] != "undefined") {
			// Show message
			loginBox.setFormErrorReport(jq(this), response.body.error.payload.message);
		} else if (typeof response.body['update'] != "undefined") {
			// Get status and set report message
			var status = response.body.update.payload.status;
			if (status == 1) {
				// Report, set cookie and callback
				loginBox.setFormReport(jq(this), response.body.update.payload.message);

				// Show success
				jq(".box-main").addClass("hidden");
				jq(".box-main.success").removeClass("hidden");
				jq(".box-main.success .bx-succ-title").html(response.body.update.payload.message);

				// Hide all forms and show the selected one
				setTimeout(function() {
					jq(".box-footer .ft-lnk.login").trigger("click");
				}, 2500);
			} else
				loginBox.setFormErrorReport(jq(this), response.body.update.payload.message);
		}

		// Reset form
		loginBox.resetForm(jq(this));
	});
});