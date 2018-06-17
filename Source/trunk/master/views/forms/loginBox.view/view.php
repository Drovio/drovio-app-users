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
importer::import("AEL", "Resources");
importer::import("UI", "Apps");

// Import APP Packages
application::import("Security");
//#section_end#
//#section#[view]
use \AEL\Resources\resource;
use \APP\Security\publicAppKey;
use \UI\Apps\APPContent;

// Create Application Content
$appContent = new APPContent();

// Get action factory
$actionFactory = $appContent->getActionFactory();
$appContent->build("", "loginBoxContainer", TRUE);

// Get public key
$keys = publicAppKey::getTeamKeys();
$publicKey = array_shift($keys)['akey'];

// Load script from resources
$script = resource::get("/loginbox/code/loginbox.template.js");
$script = str_replace("%{API_KEY}", $publicKey, $script);

// Loginbox in general
$script = "&lt;script&gt;\n".$script."\n&lt;/script&gt;";
$scriptHolder = HTML::select(".loginBox .ntf.box p.script")->item(0);
HTML::innerHTML($scriptHolder, $script);

$script_placeholder = resource::get("/loginbox/code/loginbox_placeholder.template.js");
$script_placeholder = str_replace("%{API_KEY}", $publicKey, $script_placeholder);


// Set try me action
$tryme = HTML::select(".loginBox .tryme")->item(0);
$actionFactory->setAction($tryme, "box/loginBox");

// Return output
return $appContent->getReport();
//#section_end#
?>