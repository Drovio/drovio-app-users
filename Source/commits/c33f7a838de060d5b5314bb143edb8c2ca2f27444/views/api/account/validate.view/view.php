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
importer::import("UI", "Content");

// Import APP Packages
application::import("Identity");
application::import("Security");
//#section_end#
//#section#[view]
use \APP\Security\privateAppKey;
use \APP\Identity\account;
use \UI\Content\JSONContent;

// Create json content
$jsonContent = new JSONContent();

// Validate the key
if (!privateAppKey::validate())
{
	// Show error
	$error = array();
	$error['message'] = "Your api key is not valid or registered in your settings.";
	return $jsonContent->getReport($error, $allowOrigin = "", $withCredentials = TRUE, $key = "error");
}

// Get user credentials
$username = engine::getVar("username");
$password = engine::getVar("password");

// Validate user with given attributes
// Must include the following:
// - acc/__DRVC_ACC
// - mx/__DRVC_MX
// - person/__DRVC_PRS
if (!account::getInstance()->validate())
{
	// Show error
	$info = array();
	$info['status'] = 0;
	$info['description'] = "The given account session is not valid!";
}
else
{
	// Account session valid
	$info = array();
	$info['status'] = 1;
	$info['description'] = "The given account session is valid!";
}

// Return report
return $jsonContent->getReport($info, $allowOrigin = "", $withCredentials = TRUE, $key = "validate");
//#section_end#
?>