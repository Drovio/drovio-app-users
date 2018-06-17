var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Make request
	jq(document).on("click", ".playDemo .demo_button", function() {
		// Get info and make request
		var method = jq(".demo_method").val();
		var url_prefix = jq(".demo_url_prefix").val();
		var url = jq(".demo_url").val();
		var requestData = jq(".demo_parameters").val();
		
		// Make request
		url = url.replace("{drovio_api}", url_prefix);
		ACProtocol.request(url, method, requestData, jq(this), function(response) {
			jq(".demo_output").val(JSON.stringify(response));
		}, null);
	});
});