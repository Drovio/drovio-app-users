// Get server ip
var ip = location.host;

console.log("called tracker function");
// Check cookies for user id
findCookieVar = function(key) {
	var cookieVars = document.cookie.split("; ");
	var key_value;
	for (cookieVar in cookieVars) { if (cookieVar.slice(0, key.length) == key) {key_value = cookieVar.slice(key.length+1);}}
	}
var user_id = findCookieVar("user_id");
// Get new user id
if (user_id == undefined) {
	// Initialize user
	var requestData = null;
	var extraOptions = null;
	JSPreloader.loadView("api/tracker/createUser", "post", requestData, this, function(response) {
		// Get user information
		user_id = response;
		document.cookie = "user_id=" + user_id;
		//loginBox.load(response);
	}, function(err) {
		console.error("There was an error generating the user id.");
	}, extraOptions);
} else {
	console.log (document.cookie);
}