var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Quick link
	jq(document).on("click", ".userList .no_users .qlink.forms", function() {
		// Click on menu
		jq(".drovioUserManagement .side_menu .menuitem.forms").trigger("click");
	});
	
	// Refresh user list
	jq(document).on("users.list.reload", function() {
		jq("#dusm_ref_users").trigger("reload");
	});
	
	// Search list
	jq(document).on("click", ".userList .actionsContainer .action.search", function() {
		// Click on menu
		jq(this).toggleClass("open");
		
		// Focus on search input
		if (jq(this).hasClass("open")) {
			var search = jq(this).find(".searchInput").focus().val();
			searchUsers(search);
		}
	});
	
	jq(document).on("click", ".userList .actionsContainer .action.search .searchInput", function(ev) {
		ev.preventDefault();
		ev.stopPropagation();
	});
	
	// Search users
	jq(document).on("keyup", ".userList .actionsContainer .action.search .searchInput", function(ev) {
		// Check if it's escape and hide search
		if (ev.which == 27) {
			jq(this).val("");
			jq(this).closest(".action").removeClass("open");
		}
		
		// Get input and search users
		var search = jq(this).val();
		searchUsers(search);
	});
	
	// Enable search
	jq(document).on("focusin", ".userList .actionsContainer .action.search .searchInput", function(ev) {
		// Get input and search users
		var search = jq(this).val();
		searchUsers(search);
	});
	
	// Search all users
	function searchUsers(search) {
		// Get grid list
		var gridList = jq(".uList");
		
		// Filter grid list
		dataGridList.filter(gridList, search);
	}
});