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
application::import("Security");
application::import("Utils");
//#section_end#
//#section#[view]
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

// Get account email to reset password
$email = engine::getVar("email");

// Check if there is an account with the given email
$accountInfo = account::getAPIInstance()->getAccountByUsername($email, $includeEmail = TRUE);
if (empty($accountInfo))
{
	// Show error
	$error = array();
	$error['message'] = "We couldn't find the given email to our identity database.";
	return $jsonContent->getReport($error, $allowOrigin = "", $withCredentials = TRUE, $key = "error");
}

// Create a reset token
$accountID = $accountInfo['id'];
$resetID = account::getAPIInstance()->generateResetId($accountID);
if (!$resetID || empty($resetID))
{
	// Show error
	$info = array();
	$info['status'] = 0;
	$info['message'] = "Something went wrong while trying to generate a reset token for your account. We created a log and we will look into this as soon as possible.";
}
else
{
	// Check if we have to send email with the process
	$sendEmail = engine::getVar("notify");
	if ($sendEmail)
		appMail::sendResetPasswordMail($email, $resetID);
	
	UserLogger::log($accountID, "reset-password", "A new password reset token has been created following request from user.");
	
	// Success message
	$info = array();
	$info['status'] = 1;
	$info['message'] = "A reset token has been successully created for this account.";
}

// Return report
return $jsonContent->getReport($info, $allowOrigin = "", $withCredentials = TRUE, $key = "reset");
//#section_end#
?>