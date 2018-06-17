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
importer::import("UI", "Presentation");

// Import APP Packages
application::import("Resources");
//#section_end#
//#section#[view]
use \UI\Apps\APPContent;
use \APP\Resources\socialLogin;
use \UI\Presentation\popups\popup;

// Create Application Content
$appContent = new APPContent();

// Build the application view content
$appContent->build("", "redirectUriContainer", TRUE);

// Set information to be shown
$type = $_GET['socialType'];
$socialLogin = new socialLogin($type);
$infoRow = HTML::create("div", $socialLogin->getRedirectUri(), "", "info");
	
// Put info in container
$appContent->append($infoRow);

// Create popup
$pp = new popup();
$pp->type($type = popup::TP_OBEDIENT, $toggle = FALSE);
$pp->background(TRUE);
$pp->build($appContent->get());

return $pp->getReport();
//#section_end#
?>