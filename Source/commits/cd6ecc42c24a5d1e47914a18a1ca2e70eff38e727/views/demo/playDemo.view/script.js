var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Make request
	jq(document).on("click", ".playDemo .demo_button", function() {
		// Get info and make request
		var method = jq(".demo_method").val();
		var api_url = jq(".demo_api_url").val();
		var url = jq(".demo_url").val();
		var requestData = jq(".demo_parameters").val();
		
		// Make request
		url = api_url + url;
		ACProtocol.request(url, method, requestData, jq(this), function(response) {
			jq(".demo_output").val(JSON.stringify(response));
		}, null);
	});
});