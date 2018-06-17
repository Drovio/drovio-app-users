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
importer::import("AEL", "Identity");

// Import APP Packages
application::import("Resources");
//#section_end#
//#section#[view]
use \AEL\Identity\SocialLoginManager;
use \AEL\Identity\account;
use \APP\Resources\socialLogin;

if (engine::isPost())
{
	// Set response status code
	$jsonContent->setResponseCode($code = 405);
	
	// Show error
	$error = array();
	$error['message'] = "You are using a wrong request method for this call. Use GET.";
	return $jsonContent->getReport($error, $allowOrigin = "", $withCredentials = TRUE, $key = "error");
}

// Check code
$accessCode = engine::getVar("code");
if (empty($accessCode))
{
	// Set response status code
	$jsonContent->setResponseCode($code = 400);
	
	// Show error
	$error = array();
	$error['message'] = "Your request is missing the 'code' parameter.";
	return $jsonContent->getReport($error, $allowOrigin = "", $withCredentials = TRUE, $key = "error");
}

// Get code
$fbSettings = new socialLogin("facebook");
$client_id = $fbSettings->get('client_id');
$client_secret = $fbSettings->get('client_secret');
$redirect_uri = $fbSettings->getRedirectUri();

// Login
$result = SocialLoginManager::facebookLogin($accessCode, $client_id, $client_secret, $redirect_uri);
if ($result)
{
	// Login success full, get authentication token and redirect
	$redirect_after_signin = trim($fbSettings->get('redirect_after_signin'), "/");
	return header("Location: ".$redirect_after_signin."?auth_token=".$result);
}

// Error in login
return $result;
//#section_end#
?>