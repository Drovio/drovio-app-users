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
importer::import("AEL", "Security");
importer::import("UI", "Apps");

// Import APP Packages
application::import("Resources");
//#section_end#
//#section#[view]
use \AEL\Security\privateKey;
use \UI\Apps\APPContent;
use \APP\Resources\settings;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "playDemoContainer", TRUE);

// Set values according to mode
$mode = engine::getVar("mode");

// Get application id and a private key
$applicationID = application::init();
$privateKeys = privateKey::getTeamKeys();
$privateKey = $privateKeys[0]['akey'];

// Get demo password
$settings = new settings();
$demoPwd = $settings->get("demo_pwd");
// Set url prefix
$urlPrefix = "https://api.drov.io/apps/".$applicationID."/".$privateKey."/";

// Set config
$config = array();
// Login configuration
$mconfig = array();
$mconfig['method'] = "post";
$mconfig['url'] = "{drovio_api}/api/login";
$mconfig['parameters'] = "username=demo@identity.drov.io&password=".$demoPwd;
$config['login'] = $mconfig;
// User info configuration
$mconfig = array();
$mconfig['method'] = "get";
$mconfig['url'] = "{drovio_api}/api/account/info";
$mconfig['parameters'] = "auth_token=REPLACE_WITH_AUTH_TOKEN";
$config['uinfo'] = $mconfig;

// Get configuration according to mode
$mode = engine::getVar("mode");
$mode = (empty($mode) ? "login" : $mode);
$selectedConfig = $config[$mode];

// Set url prefix
$prefix = HTML::select(".demo_url_prefix")->item(0);
HTML::attr($prefix, "value", $urlPrefix);

// Set values
$method = $config[$mode]["method"];
$option = HTML::select(".demo_method option[value='".$method."']")->item(0);
HTML::attr($option, "selected", "selected");

$url = HTML::select(".demo_url")->item(0);
HTML::attr($url, "value", $config[$mode]["url"]);

$parameters = HTML::select(".demo_parameters")->item(0);
HTML::innerHTML($parameters, $config[$mode]["parameters"]);

// Return output
return $appContent->getReport();
//#section_end#
?>