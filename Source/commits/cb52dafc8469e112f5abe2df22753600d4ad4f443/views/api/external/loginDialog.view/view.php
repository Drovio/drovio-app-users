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
importer::import("UI", "Content");

// Import APP Packages
application::import("Identity");
application::import("Security");
//#section_end#
//#section#[view]
use \APP\Security\publicAppKey;
use \APP\Identity\account;
use \UI\Content\JSONContent;
use \UI\Apps\APPContent;

// Create json content
$jsonContent = new JSONContent();

// Get host origin
$hostOrigin = engine::getVar("origin");

// Validate the key
if (!publicAppKey::validate())
{
	// Show error
	$error = array();
	$error['message'] = "Your api key is not valid or registered in your settings.";
	return $jsonContent->getReport($error, $allowOrigin = $hostOrigin, $withCredentials = TRUE, $key = "error");
}

// Check if origin is accepted
$hostOrigin = engine::getVar("origin");
if (!publicAppKey::validateOrigin($hostOrigin))
{
	// Show error
	$error = array();
	$error['message'] = "Your api key is not valid or registered in your settings.";
	return $jsonContent->getReport($error, $allowOrigin = $hostOrigin, $withCredentials = TRUE, $key = "error");
}


// Create login dialog
$appContent = new APPContent();

// Build the application view content
$appContent->build("", "identityLoginDialogContainer", TRUE);

// Return output
return $appContent->getReport();

$loginDialog = array();
$loginDialog['test'] = "success";

// Return report
return $jsonContent->getReport($loginDialog, $allowOrigin = $hostOrigin, $withCredentials = TRUE, $key = "login_dialog");
//#section_end#
?>