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
$appContent->build("", "formExplorerContainer", TRUE);

// Load wizard for each form
$forms = array();
$forms[] = "registration";
$forms[] = "login";
$forms[] = "recover";
foreach ($forms as $formName)
{
	// Get menu item
	$menuItem = HTML::select(".wz-menu .wz-menu-item.".$formName)->item(0);
	
	// Set static navigation
	$appContent->setStaticNav($menuItem, $ref = "", $targetcontainer = "", $targetgroup = "", $navgroup = "wzfgroup", $display = "none");
	
	// Load wizard on click
	$attr = array();
	$attr['fname'] = $formName;
	$actionFactory->setAction($menuItem, "forms/formWizard", ".wizardContainer", $attr, $loading = TRUE);
}

// Return output
return $appContent->getReport();
//#section_end#
?>