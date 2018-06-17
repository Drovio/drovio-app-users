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
application::import("Mail");
application::import("Resources");
application::import("Security");
application::import("Utils");
//#section_end#
//#section#[view]
use \APP\Resources\settings;
use \APP\Security\appKey;
use \APP\Identity\account;
use \APP\Mail\appMail;
use \APP\Utils\DayLogger;
use \APP\Utils\UserLogger;
use \AEL\Profiler\logger;
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
	// Log error
	logger::getInstance()->log("API key is not valid.", logger::ERROR, $_POST);
	
	// Show error
	$error = array();
	$error['message'] = "Your api key is not valid or registered in your settings.";
	return $jsonContent->getReport($error, $allowOrigin = "", $withCredentials = TRUE, $key = "error");
}

// Get user information
$email = engine::getVar("email");
$firstname = engine::getVar("firstname");
$lastname = engine::getVar("lastname");
$password = engine::getVar("password");

// Validate email
if (empty($email))
{
	// Log error
	logger::getInstance()->log("Registration email is not valid. Aborting.", logger::ERROR, $_POST);
	
	// Show error
	$error = array();
	$error['message'] = "The email given is empty or not valid.";
	return $jsonContent->getReport($error, $allowOrigin = "", $withCredentials = TRUE, $key = "error");
}


// Create user
$status = account::getAPIInstance()->create($email, $firstname, $lastname, $password);
if (!$status)
{
	// Show error
	$info = array();
	$info['status'] = 0;
	
	// Check if there is an account with the same email
	$accountInfo = account::getAPIInstance()->getAccountByUsername($email, $includeEmail = TRUE);
	if (!empty($accountInfo))
	{
		// Log error
		logger::getInstance()->log("The email provided already exists in the database. Aborting.", logger::ERROR, $_POST);
		
		// Add error message
		$info['message'] = "The email provided already exists in the database.";
	}
	else
	{
		// Log error
		logger::getInstance()->log("There was a problem in the registration. Aborting.", logger::ERROR, $_POST);
		
		// Add error message
		$info['message'] = "There was a problem in the registration. Please try again.";
	}
}
else
{
	// Get account info
	$accountInfo = account::getAPIInstance()->getAccountByUsername($email, $includeEmail = TRUE);
	unset($accountInfo['password']);
	
	// Log event
	DayLogger::log("signup", $accountInfo['email']." signed up.");
	UserLogger::log($accountInfo['id'], "signup", $accountInfo['mail']." signed up for our service.");
	
	// Send welcome email
	$settings = new settings();
	$sendWelcome = $settings->get('SEND-WELCOME-EMAIL');
	if ($sendWelcome == 1) {
		appMail::sendWelcomeEmail($email);
	}
	
	// Check if there is a request to login as well
	$loginUser = engine::getVar("login");
	if ($loginUser) {
		account::getAPIInstance()->login($email, $password);
		UserLogger::log($accountInfo['id'], "login", "User logged in.");
	}
	
	// Successfull login
	$info = array();
	$info['status'] = 1;
	$info['message'] = "Account created successfully!";
	$info['account_info'] = $accountInfo;
}

// Return report
return $jsonContent->getReport($info, $allowOrigin = "", $withCredentials = TRUE, $key = "register");
//#section_end#
?>