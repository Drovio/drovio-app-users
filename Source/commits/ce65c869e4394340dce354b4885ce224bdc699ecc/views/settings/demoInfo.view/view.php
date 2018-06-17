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

// Import APP Packages
application::import("Resources");
//#section_end#
//#section#[view]
use \APP\Resources\settings;
use \UI\Apps\APPContent;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "settings_demoInfoContainer", TRUE);

// Get application settings
$settings = new settings();

// Get demo password
$attr = array();
$attr['pwd'] = $settings->get("demo_pwd");
$literal = $appContent->getLiteral("settings.demo", "lbl_demo_password", $attr);
$holder = HTML::select(".demoInfo .code")->item(0);
HTML::append($holder, $literal);

// Return output
return $appContent->getReport();
//#section_end#
?>