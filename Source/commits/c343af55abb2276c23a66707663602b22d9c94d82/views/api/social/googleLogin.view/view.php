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
importer::import("UI", "Apps");

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
$gglSettings = new socialLogin("google");
$client_id = $gglSettings->get('client_id');
$client_secret = $gglSettings->get('client_secret');
$redirect_uri = $gglSettings->getRedirectUri();

// Login
$authToken = SocialLoginManager::googleLogin($accessCode, $client_id, $client_secret, $redirect_uri);
if ($authToken)
{
	// Login success full, get authentication token and redirect
	$redirect_after_signin = trim($gglSettings->get('redirect_after_signin'), "/");
	
	// Set header location
	$urlParams = array();
	$urlParams['auth_token'] = $authToken;
	$urlParams['login'] = 1;
	$urlParams['from'] = "google";
	return header("Location: ".$redirect_after_signin."?".http_build_query($urlParams));
}

// Error in login
return $authToken;
//#section_end#
?>