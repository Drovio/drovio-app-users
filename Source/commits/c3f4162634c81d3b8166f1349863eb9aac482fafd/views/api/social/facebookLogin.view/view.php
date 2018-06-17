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
importer::import("AEL", "Security");

// Import APP Packages
application::import("Resources");
//#section_end#
//#section#[view]
use \AEL\Identity\SocialLoginManager;
use \AEL\Identity\account;
use \AEL\Security\publicKey;
use \APP\Resources\socialLogin;

if (engine::isPost())
{
	// Show error
	$error = array();
	$error['message'] = "You are using a wrong request method for this call. Use GET.";
	return $jsonContent->getReport($error, $allowOrigin = "", $withCredentials = TRUE, $key = "error");
}

// Get code
if (isset($_GET['code'])) {
	// Get client parameters
	
	$code = $_GET['code'];
	$fbSettings = new socialLogin("facebook");
	$client_id = $fbSettings->get('client_id');
	$client_secret = $fbSettings->get('client_secret');
	$redirect_after_signin = trim($fbSettings->get('redirect_after_signin'), "/");
	
	// Get redirect_uri
	$redirect_uri = $fbSettings->getRedirectUri();
	$result = SocialLoginManager::facebookLogin($code, $client_id, $client_secret, $redirect_uri);
	if (is_string($result)) {
		// SUCCESS
		return header("Location: $redirect_after_signin/?auth_token=$result");
	}
	return $result;
}

// Show error
$error = array();
$error['message'] = "Your request is missing the 'code' parameter.";
return $jsonContent->getReport($error, $allowOrigin = "", $withCredentials = TRUE, $key = "error");
//#section_end#
?>