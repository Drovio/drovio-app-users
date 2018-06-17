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
importer::import("UI", "Presentation");

// Import APP Packages
//#section_end#
//#section#[view]
use \UI\Apps\APPContent;
use \UI\Presentation\popups\popup;

// Create Application Content
$appContent = new APPContent();

// Build the application view content
$appContent->build("", "demoPopupContainer", TRUE);
$actionFactory = $appContent->getActionFactory();

// Load demo forms with pre-defined settings
$ditems = array();
$ditems[] = "login";
$ditems[] = "uinfo";
foreach ($ditems as $class)
{
	// Get menu item
	$mItem = HTML::select(".demoPopup .demo-menu .demo-menu-item.".$class)->item(0);
	
	// Set navigation ref
	$appContent->setStaticNav($mItem, "", $targetcontainer = "", $targetgroup = "", $navgroup = "demo-group", $display = "none");
	
	// Set action
	$attr = array();
	$attr['mode'] = $class;
	$actionFactory->setAction($mItem, "demo/playDemo", ".demoPopup .demoContainer", $attr);
}

// Create popup
$pp = new popup();
$pp->type($type = popup::TP_PERSISTENT, $toggle = FALSE);
$pp->background(TRUE);
$pp->build($appContent->get());

return $pp->getReport();
//#section_end#
?>