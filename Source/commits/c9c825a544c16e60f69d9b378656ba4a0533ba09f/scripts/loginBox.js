if (typeof JSPreloader != "undefined") {
	// Get login dialog
	var requestData = null;
	var extraOptions = null;
	JSPreloader.loadView("api/external/loginBox", "get", requestData, this, function(response) {
		// Initialize login box
		loginBox.init(response);
	}, function(err) {
		console.error("There was an error loading the given application view.");
	}, extraOptions);
	
	// Load loginBox css
	JSPreloader.loadStyle("loginBox");

	loginBox = {
		box: null,
		init: function(response) {
			// Get login dialog
			this.box = response.body[0].payload.content;
			
			// Load resources
			for (var key in response.head['bt_rsrc']) {
				// Get resource info
				var resource = response.head['bt_rsrc'][key];
				
				// Get loginBox resource
				if (resource.attributes.package == "api/external/loginBox") {
					loginBox.loadCSS(resource.css.replace("https", "http"));
					loginBox.loadJS(resource.js.replace("https", "http"));
				}
			}
			
			jq(document).on("login-box-dispose", function() {
				loginBox.disposePopup();
			});
		},
		load: function(placeholder) {
			// Check if placeholder is empty
			if (typeof placeholder == 'undefined' || placeholder == null) {
				// Show popup
				loginBox.showPopup(this.box);
			} else {
				// Append to placeholder
				jq(placeholder).append(this.box);
			}
		},
		loadJS : function(href, callback) {
			jq.getScript(href, function(ev) {
				// run successCallback function, if any
				if (typeof callback == 'function') {
					callback.call();
				}
			});
		},
		loadCSS : function(href) {
			return jq("<link rel='stylesheet' href='"+href+"'>").appendTo(jq("head"));
		},
		showPopup: function(popupContent) {
			// Check and remove any previous overlays
			jq(".login-popup-overlay").detach();
			
			// Create popup overlay
			var popupOverlay = jq("<div />").addClass("login-popup-overlay");
			
			// Get left margin
			var marginLeft = "";
			
			// Add popup content
			var uiPopup = jq("<div />").addClass("login-popup").append(jq(popupContent).addClass("popup-content"));
			
			// Append
			popupOverlay.append(uiPopup).appendTo(jq(document.body));
			
			// Adjust position
			var contentWidth = jq(uiPopup).outerWidth();
			uiPopup.css("margin-left", -contentWidth/2 + "px");
		},
		disposePopup: function() {
			jq(".login-popup-overlay").detach();
		},
		submitForm : function(ev, jqForm, successCallback) {
			// Check if form is already posting
			if (jqForm.data("posting") == true)
				return false;

			// Initialize posting
			jqForm.data("posting", true);

			// Clear form report
			jqForm.find(".formReport").empty();

			// Form Parameters
			var formData = "";
			if (jqForm.attr('enctype') == "multipart/form-data") {
				// Initialize form data
				formData = new FormData();

				// Get form data
				var fdArray = jqForm.serializeArray();
				for (index in fdArray)
					formData.append(fdArray[index].name, fdArray[index].value);

				// Get files (if any)
				jqForm.find("input[type='file']").each(function() {
					if (jq.type(this.files[0]) != "undefined")
						formData.append(jq(this).attr("name"), this.files[0]);
				});
			}
			else
				formData = jqForm.serialize();

			// Disable all inputs
			jqForm.find("input[name!=''],select[name!=''],textarea[name!=''],button").prop("disabled", true).addClass("disabled");

			// Set Complete callback Handler function
			var completeCallback = function(ev) {
				// Enable inputs again
				jqForm.find("input[name!=''],select[name!=''],textarea[name!=''],button").prop("disabled", false).removeClass("disabled");

				// Set posting status false
				jqForm.data("posting", false);
			};
			
			// Create extra options
			var options = {
				completeCallback: completeCallback,
				withCredentials: true
			}

			// Start HTMLServerReport request
			var formAction = jqForm.attr("action");
			JSPreloader.request(formAction, "POST", formData, jqForm, function(response) {
				// Execute custom callback (if any)
				if (typeof successCallback == 'function') {
					successCallback.call(this, ev, response);
				}
			}, options);
		},
	}
}