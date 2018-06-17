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
use \UI\Apps\APPContent;
use \UI\Content\JSONContent;

$jsonContent = new JSONContent();

// Check request
if (!engine::isPost()) {
	// Show error
	$error = array();
	$error['message'] = "Current request is not valid. POST request expected.";
	return $jsonContent->getReport($error, $allowOrigin = "", $withCredentials = TRUE, $key = "error");
}

$tracker = TrackerSource::getInstance();

// TODO: remove: engine::getVar('user_id');
// if user is empty then create new user

// TODO: if session is empty then create new session
// Throw error key parameter are missing
if (empty($_POST['user_id']) || empty($_POST['session_id']) || empty($_POST['time'])|| FALSE) {
	// Show error
	$error = array();
	$error['message'] = "You are missing key parameters values";
	return $jsonContent->getReport($error, $allowOrigin = "", $withCredentials = TRUE, $key = "error");
}

// Prepare event description
$eventKeys = array('type', 'time', 'user_id', 'session_id', 'platform', 'target_tag', 'target_id', 'target_class', 'href', 'domain', 'hash', 'path', 'query', 'title', 'action_method', 'view_controller', 'view_controller_accessibility_identifier', 'view_controller_accessibility_label', 'target_view_class', 'target_view_name', 'target_accessibility_identifier', 'target_accessibility_label', 'target_text');                                    

$eventParams = array();
foreach ($eventKeys as $key) {
	if (empty($_POST[$key])) {
		continue;
	}
	$eventParams[$key] = $_POST[$key];
}

// Record event
return $tracker->recordEvent($eventParams['user_id'], $eventParams);
//#section_end#
?>