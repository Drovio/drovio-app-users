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
importer::import("UI", "Presentation");

// Import APP Packages
application::import("Resources");
//#section_end#
//#section#[view]
use \UI\Presentation\dataGridList;
use \UI\Apps\APPContent;
use \APP\Resources\settings;
use \UI\Html\components\weblink;
use \UI\Interactive\forms\switchButton;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "social-login-settingsContainer", TRUE);

// Create a grid list object
$gridList = new dataGridList($editable = FALSE);

// This is a UIObject, so we build
// It supports the chain pattern, so we get the element immediately
$userList = $gridList->build($id = "socialLoginSettings", $checkable = FALSE, $withBorder = TRUE)->get();

// Append to body
DOM::append($body, $userList);

// Before adding any rows (including the headers), we can customize the column width ratio
$ratios = array();
$ratios[] = 0.4;
$ratios[] = 0.3;
$ratios[] = 0.2;
$ratios[] = 0.1;
$gridList->setColumnRatios($ratios);

// Add headers, they must be inserted before any row
$headers = array();
$headers[] = "Social network";
$headers[] = "Edit details";
$headers[] = "Redirect Uri";
$headers[] = "No/Off";
$gridList->setHeaders($headers);

$socials = array("facebook", "google");

// Get users and insert them into the list
foreach ($socials as $social)
{
	$settings = new settings();
	if ($settings->get($social.'-authenticate') == '1') {
		$enabled = TRUE;
	} else {
		$enabled = FALSE;
	}
	// Create the row to insert into the list
	$row = array();
	// Add info into the row
	$row[] = DOM::create("div", "",  "", "social-logo ".$social);
	
	// Edit credentials
	$uaction = DOM::create("div", "Edit Credentials", "", "fld uaction");
	$row[] = $uaction;
	$attr = array();
	$attr['type'] = $social;
	$actionFactory->setAction($uaction, "settings/socialCredentialsDialog", "", $attr);
	
	// Get Redirect Uri
	$uaction = DOM::create("div", "Redirect URI", "", "fld uaction");
	$row[] = $uaction;
	$actionFactory->setAction($uaction, "settings/showRedirectUri", "", array("socialType" => $social));
	
	// On/Off switch
	$switch = new switchButton($social."-switch");
	$swAttr = array();
	$swAttr['type'] = $social;
	if ($enabled) {
		$swAttr['enabled'] = 1;
	} else {
		$swAttr['enabled'] = 0;
	}
	$switchObject = $switch->build("", $enabled)->engageApp("settings/socialLoginToggle", $swAttr)->get();
	$row[] = $switchObject;
	
	// Insert the row
	$gridList->insertRow($row, $checkName = NULL, $checked = FALSE, $checkValue = "");
}

$appContent->append($gridList->get());

return $appContent->getReport();
//#section_end#
?>