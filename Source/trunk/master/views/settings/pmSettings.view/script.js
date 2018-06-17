var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Show page notification
	jq(document).on("settings.permissions.notification", function(ev, title) {
		pageNotification.show(jq(document), "settings.permissions.notification", title, "", null, null, true);
	});
});