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
use \AEL\Resources\appSettings;
use \UI\Apps\APPContent;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "drovioUserManagementApplication");

// Check application settings for the team
$settings = new appSettings($mode = appSettings::TEAM_MODE, $shared = FALSE, $settingsFolder = "/Settings/", $filename = "settings");
$allSettings = $settings->get();
if (empty($allSettings))
{
	// Load start screen
	$mView = $appContent->loadView("StartScreen");
	$appContent->append($mView);
}
else
{
	// Load main screen
	$mView = $appContent->loadView("MainView");
	$appContent->append($mView);
}

// Return output
return $appContent->getReport();
//#section_end#
?>