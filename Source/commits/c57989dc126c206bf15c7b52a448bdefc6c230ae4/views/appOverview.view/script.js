var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Quick links
	jq(document).on("click", ".appOverview .ql.users", function() {
		// Click on menu
		jq(".userManagement .side_menu .menuitem.users").trigger("click");
	});
	jq(document).on("click", ".appOverview .ql.forms", function() {
		// Click on menu
		jq(".userManagement .side_menu .menuitem.forms").trigger("click");
	});
	jq(document).on("click", ".appOverview .ql.api", function() {
		// Click on menu
		jq(".userManagement .side_menu .menuitem.api").trigger("click");
	});
});