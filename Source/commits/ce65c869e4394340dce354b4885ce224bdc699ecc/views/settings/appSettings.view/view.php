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
//#section_end#
//#section#[view]
use \UI\Apps\APPContent;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "applicationSettingsContainer", TRUE);
$settingsList = HTML::select(".applicationSettings .settings-list")->item(0);

// Set navigation
$menuItems = array();
$menuItems['billing'] = "settings/billing/billingInfo";
$menuItems['demo'] = "settings/demoInfo";
$menuItems['keys'] = "settings/keys/applicationKeys";
foreach ($menuItems as $class => $viewName)
{
	// Get menu item
	$refID = "ref_".$class;
	$navItem = HTML::select(".applicationSettings .navigation .navitem.".$class)->item(0);
	
	// Set navigation ref
	$appContent->setStaticNav($navItem, $refID, $targetcontainer = "settings-list", $targetgroup = "dusm_stgroup", $navgroup = "stgroup", $display = "none");
	
	// Load application view containe
	$viewContainer = $appContent->getAppViewContainer($viewName, $attr = array(), $startup = TRUE, $containerID = $refID, $loading = FALSE, $preload = TRUE);
	$appContent->setNavigationGroup($viewContainer, "dusm_stgroup");
	DOM::append($settingsList, $viewContainer);
}

// Return output
return $appContent->getReport();
//#section_end#
?>