if (typeof JSPreloader != "undefined") {
	// Get login dialog
	var requestData = null;
	var extraOptions = null;
	JSPreloader.loadView("api/external/loginDialog", "get", requestData, this, function(response) {
		// Parse response
		DrovioLoginDialog.load(response);
	}, function(err) {
		console.error("There was an error loading the given application view.");
	}, extraOptions);

	DrovioLoginDialog = {
		load: function(response) {
			// Get login dialog
			console.log(response);
		}
	}
}