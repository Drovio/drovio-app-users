<?php
//#section#[header]
// Use Important Headers
use \API\Platform\importer;
use \API\Platform\engine;
use \Exception;

// Check Platform Existance
if (!defined('_RB_PLATFORM_')) throw new Exception("Platform is not defined!");

// Import DOM, HTML
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

use \UI\Html\DOM;
use \UI\Html\HTML;

// Import application for initialization
importer::import("AEL", "Platform", "application");
use \AEL\Platform\application;

// Increase application's view loading depth
application::incLoadingDepth();

// Set Application ID
$appID = 89;

// Init Application and Application literal
application::init(89);
// Secure Importer
importer::secure(TRUE);

// Import SDK Packages
importer::import("AEL", "Resources");
importer::import("UI", "Apps");

// Import APP Packages
//#section_end#
//#section#[view]
use \UI\Apps\APPContent;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "userManagementContainer", TRUE);

// Set navigation
$menuItems = array();
$menuItems['overview'] = "appOverview";
$menuItems['users'] = "users/userList";
$menuItems['api'] = "documentation/apiDocumentation";
$menuItems['forms'] = "forms/embeddedForms";
$menuItems['settings'] = "appSettings";
$menuItems['about'] = "AboutView";
$mainContent = HTML::select(".userManagement .mainContent")->item(0);
foreach ($menuItems as $class => $viewName)
{
	// Get menu item
	$mItem = HTML::select(".userManagement .sidebar .menuitem.".$class)->item(0);
	
	// Set navigation ref
	$ref = "dusm_ref_".$class;
	$appContent->setStaticNav($mItem, $ref, $targetcontainer = "appMainContent", $targetgroup = "dusm_tgroup", $navgroup = "ugroup", $display = "none");
	DOM::data($mItem, "ref", $ref);
	
	$preload = FALSE;
	if (HTML::hasClass($mItem, "selected"))
		$preload = TRUE;
	$mContainer = $appContent->getAppViewContainer($viewName, $attr = array(), $startup = FALSE, $ref, $loading = TRUE, $preload);
	DOM::append($mainContent, $mContainer);
	
	// Set navigation target group
	$appContent->setNavigationGroup($mContainer, "dusm_tgroup");
}

// Return output
return $appContent->getReport();
//#section_end#
?>