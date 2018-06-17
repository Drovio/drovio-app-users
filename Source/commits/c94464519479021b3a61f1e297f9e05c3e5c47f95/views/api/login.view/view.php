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
importer::import("AEL", "Security");
importer::import("UI", "Apps");
importer::import("UI", "Content");

// Import APP Packages
//#section_end#
//#section#[view]
use \AEL\Security\appKey;
use \UI\Content\JSONContent;

// Create json content
$jsonContent = new JSONContent();

if (!engine::isPost())
{
	// Show error
	$error = array();
	$error['message'] = "You are using a wrong request method for this call. Use POST.";
	return $jsonContent->getReport($error, $allowOrigin = "", $withCredentials = TRUE, $key = "error");
}

// Get api key to get team
$apikey = engine::getVar("akey");


// Get user credentials
$username = engine::getVar("username");
$password = engine::getVar("password");

// Authenticate user
$info = array();
$info['userInfo'] = "user info";
$info['notification_settings'] = "nsdfasdfs";

// After we have created the array, we send the report to the client
// We leave the other parameters as is in order to work with the platform
// Those parameters can be used in combination with the Redback API Protocol to serve content to other apps
return $jsonContent->getReport($info, $allowOrigin = "", $withCredentials = TRUE, $key = "cat");
//#section_end#
?>