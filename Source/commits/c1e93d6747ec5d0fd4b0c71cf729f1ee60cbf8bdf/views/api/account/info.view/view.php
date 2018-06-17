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
application::import("Security");
//#section_end#
//#section#[view]
use \APP\Security\privateAppKey;
use \APP\Identity\account;
use \AEL\Profiler\logger;
use \UI\Content\JSONContent;

// Create json content
$jsonContent = new JSONContent();

// Validate the key
if (!privateAppKey::validate())
{
	// Set response status code
	$jsonContent->setResponseCode($code = 401);
	
	// Log error
	$akey = engine::getVar("akey");
	logger::getInstance()->log("API key '".$akey."'is not valid.", logger::ERROR);
	
	// Show error
	$error = array();
	$error['message'] = "Your api key is not valid or registered in your settings.";
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

// Get current account id
$accountID = account::getInstance()->getAccountID();

// Get logged in account information
$accountInfo = account::getInstance()->info($accountID);
unset($accountInfo['password']);
unset($accountInfo['reset']);

// Return report
return $jsonContent->getReport($accountInfo, $allowOrigin = "", $withCredentials = TRUE, $key = "account_info");
//#section_end#
?>