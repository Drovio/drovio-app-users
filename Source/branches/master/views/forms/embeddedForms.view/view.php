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
$appContent->build("", "embeddedFormsContainer", TRUE);

// Set navigation
$menuItems = array();
$menuItems['registration'] = "reg_form";
$menuItems['login'] = "login_form";
$menuItems['recovery'] = "rec_form";
$menuItems['social'] = "social_plg";
foreach ($menuItems as $class => $refID)
{
	// Get menu item
	$navItem = HTML::select(".embeddedForms .navigation .navitem.".$class)->item(0);
	
	// Set navigation ref
	$appContent->setStaticNav($navItem, $refID, $targetcontainer = "forms-list", $targetgroup = "dusm_fgroup", $navgroup = "ebdfgroup", $display = "none");
	
	// Set navigation target group
	$fContainer = HTML::select("#".$refID)->item(0);
	$appContent->setNavigationGroup($fContainer, "dusm_fgroup");
}

// Return output
return $appContent->getReport();
//#section_end#
?>