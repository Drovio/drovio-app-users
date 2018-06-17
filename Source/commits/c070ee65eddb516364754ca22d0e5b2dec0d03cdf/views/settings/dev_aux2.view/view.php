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
importer::import("UI", "Interactive");

// Import APP Packages
application::import("Resources");
//#section_end#
//#section#[view]
use \UI\Interactive\forms\switchButton;
use \APP\Resources\settings;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Get status
    	$settings = new settings();
	$socialType = $_POST['type'];
    	$status = ($settings->get($socialType.'-authenticate') == '1');
	
	// Activate or deactivate
	if ($status == FALSE)
		$settings->set($socialType.'-authenticate', 1);
	else
		$settings->set($socialType.'-authenticate', 0);

	// Return report status
	return switchButton::getReport(!$status);
}
return FALSE;
//#section_end#
?>