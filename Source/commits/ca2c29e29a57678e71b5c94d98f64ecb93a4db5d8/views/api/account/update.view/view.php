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
use \APP\Security\appKey;
use \APP\Identity\account;
use \APP\Identity\person;
use \UI\Content\JSONContent;

// Create json content
$jsonContent = new JSONContent();

// Check request method
if (!engine::isPost())
{
	// Show error
	$error = array();
	$error['message'] = "You are using a wrong request method for this call. Use POST.";
	return $jsonContent->getReport($error, $allowOrigin = "", $withCredentials = TRUE, $key = "error");
}

// Validate the key
if (!appKey::validate())
{
	// Show error
	$error = array();
	$error['message'] = "Your api key is not valid or registered in your settings.";
	return $jsonContent->getReport($error, $allowOrigin = "", $withCredentials = TRUE, $key = "error");
}

// Get account information to update
$firstname = engine::getVar("firstname");
$middlename = engine::getVar("middle_name");
$lastname = engine::getVar("lastname");

// Updpate person information
person::getAPIInstance()->updateInfo($firstname, $lastname, $middlename);
if (!$status)
{
	// Show error
	$info = array();
	$info['status'] = 0;
	
	// Check if there is an account already logged in
	if (account::getAPIInstance()->validate())
		$info['message'] = "Login error. There is an account already logged in. Call logout script and login again.";
	else
		$info['message'] = "Authentication error. The given credentials don't match any user in our database. Please try again.";
}
else
{
	// Get account info
	$accountInfo = account::getAPIInstance()->getAccountByUsername($username, $includeEmail = TRUE);
	unset($accountInfo['password']);
	
	// Successfull login
	$info = array();
	$info['status'] = 1;
	$info['message'] = "Login successful!";
	$info['account_info'] = $accountInfo;
}

// Return report
return $jsonContent->getReport($info, $allowOrigin = "", $withCredentials = TRUE, $key = "login");
//#section_end#
?>