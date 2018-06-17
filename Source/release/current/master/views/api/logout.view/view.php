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
importer::import("AEL", "Profiler");
importer::import("UI", "Content");

// Import APP Packages
application::import("Identity");
application::import("Utils");
//#section_end#
//#section#[view]
use \APP\Identity\account;
use \APP\Utils\UserLogger;
use \AEL\Profiler\logger;
use \UI\Content\JSONContent;

// Create json content
$jsonContent = new JSONContent();

// Check request method
if (!engine::isPost())
{
	// Set response status code
	$jsonContent->setResponseCode($code = 405);
	
	// Show error
	$error = array();
	$error['message'] = "You are using a wrong request method for this call. Use POST.";
	return $jsonContent->getReport($error, $allowOrigin = "", $withCredentials = TRUE, $key = "error");
}

// Validate account
if (!account::getInstance()->validate())
{
	// Set response status code
	$jsonContent->setResponseCode($code = 403);
	
	// Show error
	$error = array();
	$error['message'] = "Current account session is not valid.";
	return $jsonContent->getReport($error, $allowOrigin = "", $withCredentials = TRUE, $key = "error");
}

// Log user activity
$userID = account::getInstance()->getAccountID();
UserLogger::log($userID, "logout", "User logged out");

// Logout user
$status = account::getInstance()->logout();

// Return success
$info = array();
$info['status'] = 1;
$info['message'] = "Logout successful!";
return $jsonContent->getReport($info, $allowOrigin = "", $withCredentials = TRUE, $key = "logout");
//#section_end#
?>