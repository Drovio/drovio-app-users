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
importer::import("UI", "Apps");

// Import APP Packages
application::import("Resources");
//#section_end#
//#section#[view]
use \UI\Apps\APPContent;
use \APP\Resources\settings;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "userManagementContainer", TRUE);

// Set navigation
$menuItems = array();
$menuItems['overview'] = "appOverview";
$menuItems['users'] = "users/userList";
$menuItems['social'] = "settings/social/socialSettings";
$menuItems['loginbox'] = "forms/loginBox";
$menuItems['forms'] = "forms/formExplorer";
$menuItems['settings'] = "settings/appSettings";
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

// Set demo action
$demoItem = HTML::select(".userManagement .sidebar .menuitem.demo")->item(0);
$actionFactory->setAction($demoItem, "demo/demoPopup");

// Check for welcome message
$settings = new settings();
$welcomeMessageShown = $settings->get("welcome_message_shown");
if (!$welcomeMessageShown)
{
	// Set welcome message as shown
	$settings->set("welcome_message_shown", 1);
	
	// Get demo account password
	$demoPassword = $settings->get("demo_pwd");

	// Set initial notification
	$demoAccount = HTML::select(".userManagementContainer .welcome_notification .ntf-body .demo")->item(0);
	$attr = array();
	$attr['pwd'] = $demoPassword;
	$title = $appContent->getLiteral("main.ntf", "lbl_demoAccount", $attr);
	DOM::append($demoAccount, $title);
}
else
{
	// Remove welcome message
	$welcome_message = HTML::select(".userManagement .welcome_notification")->item(0);
	HTML::remove($welcome_message);
}

// Return output
return $appContent->getReport();
//#section_end#
?>