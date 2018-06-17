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
application::import("Identity");
application::import("Utils");
//#section_end#
//#section#[view]
use \UI\Apps\APPContent;
use \APP\Identity\account;
use \APP\Utils\DayLogger;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "appOverviewContainer", TRUE);

$appContent->append(DOM::create("p", "1 day signups: ".DayLogger::getLastNDaysSignupCount(1), "", ""));
		    
return $appContent->getReport();
//#section_end#
?>