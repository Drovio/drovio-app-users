lgbx_cookies = {
	get : function(c_name) {
		var i, b, c;
		var ARRcookies = document.cookie.split(";");
		for (i=0; i<ARRcookies.length; i++)
		{
			b = ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
			c = ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
			b = b.replace(/^\s+|\s+$/g, "");
			if (b == c_name)
				return unescape(c);
		}

		return null;
	},
	set : function(c_name, value, exdays, path, domain) {
		var exdate = new Date();
		exdate.setDate(exdate.getDate() + exdays);
		var domain = (domain && domain !== 'localhost') ? '; domain=' + (domain) : '';
		var c_value = escape(value) + domain + "; path="+path+";" + ((exdays == null) ? "" : "expires=" + exdate.toUTCString());
		document.cookie = c_name + "=" + c_value;
	}
}

lgbx_url = {
	getVar : function(name) {
		return this.getUrlVar(window.location.href, name);
	},
	getUrlVar : function(url, name) {
		var vars = {};
		url = url.split('#')[0];
		var parts = url.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
			if (key in vars){
				if (typeof vars[key] == "string")
					vars[key] = [vars[key]];
				vars[key] = jq.merge(vars[key],[value]);
			}else
				vars[key] = value;
		});

		if (typeof(name) != "undefined")
			return vars[name];

		return vars;
	},
	removeVar : function(hrf, vrb) {
		// If URL has no variables, return it
		if (hrf.indexOf("?") == -1)
			return hrf;

		// Split variables from URI
		var hr_splitted = hrf.split("?");
		var prefix = hr_splitted[0];
		var vrbles_sec = "?" + hr_splitted[1];

		// Remove variable using patterns
		var var_patt = new RegExp(vrb+"=(?=[^&]*)[^&]*[&*]|[&*]"+vrb+"=(?=[^&]*)[^&]*|^\\?"+vrb+"=(?=[^&]*)[^&]*$", "i");
		vrbles_sec = vrbles_sec.replace(var_patt, "");
		var result = prefix + vrbles_sec;

		return result;
	},
	redirect : function(url) {
		if (false) {// if site is not trusted, prompt user

		}
		else {// If site is trusted
			window.location = url;
			window.location.href = url;
		}
	},
	getSubdomain : function() {
		var info = this.info();
		return info.sub;
	},
	getDomain : function() {
		var info = this.info();
		return info.domain;

	},
	info : function() {
		// Get init variables
		var fullHost = window.location.host;
		var parts = fullHost.split('.');

		// Set sub and domain
		var sub = "";
		var domain = "";

		if (parts[0] == "drov")
			sub = "www";
		else {
			sub = parts[0];
			parts = parts.splice(1);
		}

		domain = parts.join(".");

		var info = new Object();
		info['protocol'] = window.location.protocol.replace(":", "");
		info['sub'] = sub;
		info['domain'] = domain;

		return info;
	},
	getPathname : function() {
		return encodeURIComponent(window.location.pathname);
	},
	resolve : function(sub, url) {
		// Check if the url is already resolved
		if (url.indexOf("http") == 0)
			return url;

		// Check the subdomain
		var urlInfo = this.info();
		var urlProtocol = urlInfo['protocol'];
		var resolved_url = this.getDomain() + "/" + url;
		resolved_url = (sub == "www" ? "" : sub + ".") + resolved_url;
		return urlProtocol+"://" + resolved_url;
	}
}



id_loginBox = {
	options: {
		cookie_auth_token: "auth_token",
		cookie_duration: 30,
		cookie_domain: "",
		placeholder: null,
		preload: false,
		login_callback: null,
		register_callback: null,
		logout_callback: null
	},
	auth_token: null,
	box: null,
	setOptions: function(customOptions) {
		// Extend object options
		this.options = jq.extend(this.options, customOptions);
	},
	load: function(response) {
		// Get login dialog
		this.box = response.body[0].payload.content;

		// Load resources
		for (var key in response.head['bt_rsrc']) {
			// Get resource info
			var resource = response.head['bt_rsrc'][key];

			// Get loginBox resource
			if (resource.attributes.package == "box/loginBox") {
				loginBox.loadCSS(resource.css.replace("https", "http"));
				loginBox.loadJS(resource.js.replace("https", "http"));
			}
		}

		// Dispose loginbox
		jq(document).on("login-box-dispose", function() {
			loginBox.disposePopup();
		});

		// Trigger login box arrived
		jq(document).trigger("loginbox.ready");

		// Check for placeholder
		if (this.options.placeholder != null && this.options.preload)
			loginBox.load(this.options.placeholder);
	},
	show: function(placeholder, mode) {
		// Check if placeholder is empty
		if (typeof placeholder == 'undefined' || placeholder == null) {
			// Show popup
			var lgClone = jq(this.box).clone();
			loginBox.showPopup(lgClone);
			if (typeof mode != 'undefined' && mode != null)
				lgClone.find(".ft-lnk." + mode).trigger("click");
		} else {
			// Set options
			this.options.placeholder = placeholder;
			this.options.preload = true;
			
			// Append to placeholder
			if (this.box != null)
				jq(this.options.placeholder).append(jq(this.box).clone().find(".identity-login-box").removeClass("dialog").end());
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
		// Re-adjust position (for mobile)
		setTimeout(function() {
			var contentWidth = jq(uiPopup).outerWidth();
			uiPopup.css("margin-left", -contentWidth/2 + "px");
			uiPopup.css("left", "50%");
		}, 10);
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
	setFormReport: function(jqForm, report) {
		var ntf = jq("<div />").addClass("ntf").html(report);
		jqForm.find(".formReport").append(ntf);
	},
	setFormErrorReport: function(jqForm, report) {
		var ntf = jq("<div />").addClass("ntf").addClass("error").html(report);
		jqForm.find(".formReport").append(ntf);
	},
	clearFormReport: function(jqForm) {
		jqForm.find(".formReport").empty();
	},
	resetForm: function(jqForm, full) {
		// Reset form (full or password-only)
		if (full == 1 || full == undefined)
			jqForm.trigger('reset');
		else
			jqForm.find("input[type=password]").val("");
	},
	getAuthToken: function() {
		return this.auth_token;
	},
	setAuthToken: function(auth_token) {
		// Set token local
		this.auth_token = auth_token;
		
		// Set cookies according to settings
		lgbx_cookies.set(this.options.cookie_auth_token, this.auth_token, this.options.cookie_duration, "/", this.options.cookie_domain);
	},
	logout: function(callback) {
		// Set cookies according to settings
		lgbx_cookies.set(this.options.cookie_auth_token, null, -1);
		
		// Fallback callback
		if (typeof loginBox.options.logout_callback == 'function') {
			setTimeout(function() {
				loginBox.options.logout_callback.call();
			}, 2000);
		}
	}
}

// Extend
window.loginBox = window.loginBox || {};
window.loginBox.options = jq.extend(id_loginBox.options, window.loginBox.options);
window.loginBox = jq.extend(id_loginBox, window.loginBox);


// Check and load code
if (typeof JSPreloader != "undefined") {
	// Check if there is an authentication token
	var auth_token = lgbx_url.getVar("auth_token");
	var auth_token_cookie = lgbx_cookies.get(loginBox.options.cookie_auth_token);
	if (typeof auth_token != 'undefined') {
		// Set auth token
		loginBox.setAuthToken(auth_token);
		
		// Remove token from url
		window.history.pushState(Date.now(), "Title", lgbx_url.removeVar(window.location.href, "auth_token"));
	} else if (typeof auth_token_cookie != 'undefined' && auth_token_cookie != null) {
		// Set auth token
		loginBox.setAuthToken(auth_token_cookie);
	} else {
		// Get login dialog
		var requestData = null;
		var extraOptions = null;
		JSPreloader.loadView("box/loginBox", "get", requestData, this, function(response) {
			// Initialize login box
			loginBox.load(response);
		}, function(err) {
			console.error("There was an error loading the given application view.");
		}, extraOptions);

		// Load loginBox css
		JSPreloader.loadStyle("loginBox");
	}
}