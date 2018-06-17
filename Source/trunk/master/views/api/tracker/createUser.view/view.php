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
importer::import("UI", "Apps");
importer::import("UI", "Content");

// Import APP Packages
application::import("Tracker");
//#section_end#
//#section#[view]
use \APP\Tracker\TrackerSource;
use \UI\Content\JSONContent;

$jsonContent = new JSONContent();
print_r("Yup! got call to createUser");
if (!engine::isPost()) {
	// Show error
	$error = array();
	$error['message'] = "Current request is not valid. POST request expected.";
	return $jsonContent->getReport($error, $allowOrigin = "", $withCredentials = TRUE, $key = "error");
}

$tracker = TrackerSource::getInstance();

$userAttrs = array('user_id' => 'user_id', 
		   'joindate' => 'time', 
		   'intial_platform' => 'platform',
		   'initial_device_type' => 'device_type', 
		   'initial_country' => 'country', 
		   'initial_region' => 'region', 
		   'initial_city' => 'city', 
		   'platform' => 'platform', 
		   'intial_referrer' => 'referrer', 
		   'initial_browser'=> 'browser', 
		   'intial_landing_page' => 'landing_page', 
		   'initial_device' => 'device', 
		   'initial_carrier' => 'carrier');
$userRequiredParams = array();
foreach (array_keys($userAttrs) as $key) {
	if (empty($_POST[$userAttrs[$key]])) {
		$userRequiredParams[$key] = NULL;
	} else {
		$userRequiredParams[$key] = $_POST[$userAttrs[$key]];
	}
}
$response = $tracker->addNewUser($userRequiredParams);
$jsonContent->getReport(array("user_id" => $response), $allowOrigin = "", $withCredentials = TRUE);

//#section_end#
?>