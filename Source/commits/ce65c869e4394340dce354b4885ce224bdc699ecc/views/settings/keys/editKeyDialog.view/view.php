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
importer::import("UI", "Presentation");

// Import APP Packages
//#section_end#
//#section#[view]
use \UI\Apps\APPContent;
use \UI\Presentation\popups\popup;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Get key
$akey = engine::getVar("akey");

// Build the application view content
$appContent->build("", "apiKeyDialogContainer", TRUE);

// Load key basic information
$basicInfo = HTML::select(".apiKeyDialog .basicKeyInfo")->item(0);
$attr = array();
$attr['akey'] = $akey;
$keyInfo = $appContent->getAppViewContainer($viewName = "settings/keys/keyInfo", $attr, $startup = TRUE, $containerID = "keyInfoContainer", $loading = FALSE, $preload = TRUE);
DOM::append($basicInfo, $keyInfo);


// Create popup
$pp = new popup();
$pp->type($type = popup::TP_OBEDIENT, $toggle = FALSE);
$pp->build($appContent->get());

return $pp->getReport();
//#section_end#
?>