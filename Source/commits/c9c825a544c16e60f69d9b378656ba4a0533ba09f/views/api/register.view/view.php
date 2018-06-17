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
application::import("Utils");
//#section_end#
//#section#[view]
use \APP\Resources\settings;
use \APP\Identity\account;
use \APP\Mail\appMail;
use \APP\Utils\DayLogger;
use \APP\Utils\UserLogger;
use \AEL\Profiler\logger;
use \UI\Content\JSONContent;

// Create json content
$jsonContent = new JSONContent();

// Get host origin (if any)
$hostOrigin = engine::getVar("origin");

// Check request method
if (!engine::isPost())
{
	// Set response status code
	$jsonContent->setResponseCode($code = 405);
	
	// Show error
	$error = array();
	$error['status'] = 0;
	$error['message'] = "You are using a wrong request method for this call. Use POST.";
	return $jsonContent->getReport($error, $allowOrigin = $hostOrigin, $withCredentials = TRUE, $key = "error");
}

// Get user information
$email = engine::getVar("email");
$fullname = engine::getVar("full_name");
$firstname = engine::getVar("firstname");
$lastname = engine::getVar("lastname");
$password = engine::getVar("password");
if (isset($fullname))
{
	$nameParts = explode(" ", $fullname);
	$lastname = $nameParts[count($nameParts) - 1];
	unset($nameParts[count($nameParts) - 1]);
	$firstname = implode(" ", $nameParts);
}

// Validate email
if (empty($email))
{
	// Log error
	logger::getInstance()->log("Registration email '".$email."' is not valid. Aborting.", logger::ERROR);
	
	// Show error
	$error = array();
	$error['status'] = 0;
	$error['message'] = "The email given is empty or not valid.";
	return $jsonContent->getReport($error, $allowOrigin = $hostOrigin, $withCredentials = TRUE, $key = "error");
}

// Check if the email already exists
$accountInfo = account::getInstance()->getAccountByUsername($email, $includeEmail = TRUE);
if (!empty($accountInfo))
{
	// Show error
	$error = array();
	$error['status'] = 0;
	$error['message'] = "The email given already exists.";
	return $jsonContent->getReport($error, $allowOrigin = $hostOrigin, $withCredentials = TRUE, $key = "error");
}


// Create user
$status = account::getInstance()->create($email, $firstname, $lastname, $password);
if (!$status)
{
	// Show error
	$info = array();
	$info['status'] = 0;
	
	// Check if there is an account with the same email
	$accountInfo = account::getInstance()->getAccountByUsername($email, $includeEmail = TRUE);
	if (!empty($accountInfo))
	{
		// Add error message
		$info['message'] = "The email provided already exists in the database.";
	}
	else
	{
		// Set response status code
		$jsonContent->setResponseCode($code = 500);
		
		// Log error
		logger::getInstance()->log("There was a problem in the registration. Aborting. Post fields:", logger::ERROR, $_POST);
		
		// Add error message
		$info['message'] = "There was a problem in the registration. Please try again.";
	}
}
else
{
	// Get account info
	$accountInfo = account::getInstance()->getAccountByUsername($email, $includeEmail = TRUE);
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
	if ($loginUser)
	{
		account::getInstance()->login($email, $password);
		UserLogger::log($accountInfo['id'], "login", "User logged in.");
	}
	
	// Successfull login
	$info = array();
	$info['status'] = 1;
	$info['message'] = "Account created successfully!";
	$info['account_info'] = $accountInfo;
	
	// Add session info if user logged in
	if ($loginUser)
		$info['auth_token'] = account::getInstance()->getAuthToken();
}

// Return report
return $jsonContent->getReport($info, $allowOrigin = $hostOrigin, $withCredentials = TRUE, $key = "register");
//#section_end#
?>