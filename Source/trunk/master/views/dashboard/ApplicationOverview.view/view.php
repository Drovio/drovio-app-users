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

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "loginBoxApplicationContainer", TRUE);

// Load application dashboard
$mView = $appContent->loadView("dashboard/ApplicationDashboard");
$dashboardContainer = HTML::select(".loginBoxApplication .dashboard-container")->item(0);
DOM::append($dashboardContainer, $mView);

// Set home action to load dashboard
$homeButton = HTML::select(".bar-ico.home")->item(0);
$actionFactory->setAction($homeButton, "dashboard/ApplicationDashboard", ".dashboard-container");


// Return output
return $appContent->getReport();
//#section_end#
?>