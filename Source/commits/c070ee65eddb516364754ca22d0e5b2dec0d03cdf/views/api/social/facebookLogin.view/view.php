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
use \APP\Resources\socialLogin;

// Get code
if (!isset($_GET['code'])) {
	// Show error
	$error = array();
	$error['message'] = "'code' parameter is missing from the request.";
	return $jsonContent->getReport($error, $allowOrigin = "", $withCredentials = TRUE, $key = "error");
}
$code = $_GET['code'];

// Get client parameters
$fbSettings = new socialLogin("facebook");
$client_id = $fbSettings->get("client_id");
$client_secret = $fbSettings->get("client_secret");
$redirect_uri = $fbSettings->get("redirect_uri");

// Log user in
return SocialLoginManager::facebookLogin($code, $client_id, $client_secret, $redirect_uri);

/*	// Show error
	$error = array();
	$error['message'] = "You are using a wrong request method for this call. Use GET.";
	return $jsonContent->getReport($error, $allowOrigin = "", $withCredentials = TRUE, $key = "error");
	*/
//#section_end#
?>