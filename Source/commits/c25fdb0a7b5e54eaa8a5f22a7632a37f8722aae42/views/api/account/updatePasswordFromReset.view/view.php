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
application::import("Mail");
application::import("Resources");
application::import("Security");
application::import("Utils");
//#section_end#
//#section#[view]
use \APP\Resources\settings;
use \APP\Security\privateAppKey;
use \APP\Identity\account;
use \APP\Mail\appMail;
use \APP\Utils\UserLogger;
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
if (!privateAppKey::validate())
{
	// Show error
	$error = array();
	$error['message'] = "Your api key is not valid or registered in your settings.";
	return $jsonContent->getReport($error, $allowOrigin = "", $withCredentials = TRUE, $key = "error");
}

// Validate account
if (!account::getInstance()->validate())
{
	// Show error
	$error = array();
	$error['message'] = "Current account session is not valid.";
	return $jsonContent->getReport($error, $allowOrigin = "", $withCredentials = TRUE, $key = "error");
}

// Get current and new account password
$resetID = engine::getVar("reset_id");
$password = engine::getVar("password");

// Update account password
$status = account::getInstance()->updatePasswordByReset($resetID, $newPassword);
if (!$status)
{
	// Show error
	$info = array();
	$info['status'] = 0;
	$info['message'] = "The reset id is not valid or something went wrong. Please try again.";
}
else
{
	// Send notification email
	$settings = new settings();
	$allSettings = $settings->get();
	if ($settings->get('SEND-PASSWORD-UPDATE-CONFIRMATION-EMAIL') == 1) {
		$accountInfo = account::getInstance()->info();
		appMail::sentMailTemplate($accountInfo['mail'], $templateName = "passwordUpdateNotification.txt");
	}

	UserLogger::log($status = account::getAPIInstance()->getAccountID(), "password-update-by-reset", "User updated password using a reset token.");
	
	// Successfull update
	$info = array();
	$info['status'] = 1;
	$info['message'] = "Password updated successfully!";
}

// Return report
return $jsonContent->getReport($info, $allowOrigin = "", $withCredentials = TRUE, $key = "update");
//#section_end#
?>