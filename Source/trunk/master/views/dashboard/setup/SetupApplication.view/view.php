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
importer::import("API", "Login");
importer::import("UI", "Apps");
importer::import("UI", "Forms");

// Import APP Packages
application::import("Resources");
//#section_end#
//#section#[view]
use \API\Login\team;
use \APP\Resources\settings;
use \UI\Apps\APPContent;


// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "setupApplicationContainer", TRUE);

// Get account teams and select whether to load the create team form
$step = HTML::select(".setupApplication .step.db")->item(0);
$accountTeams = team::getTeamInstance()->getAccountTeams();
if (empty($accountTeams))
{
	// Team step
	$appView = $appContent->loadView("dashboard/setup/createTeam");
	HTML::append($step, $appView);
}
else
	HTML::remove($step);

// Settings step
$step = HTML::select(".setupApplication .step.settings")->item(0);
$appView = $appContent->loadView("dashboard/setup/saveSettings");
HTML::append($step, $appView);

// Authentication settings
$step = HTML::select(".setupApplication .step.auth")->item(0);
$appView = $appContent->loadView("dashboard/setup/authSettings");
HTML::append($step, $appView);

// Pricing Plan
$step = HTML::select(".setupApplication .step.pricing")->item(0);
$appView = $appContent->loadView("dashboard/setup/pricingPlan");
HTML::append($step, $appView);

// Return output
$appContent->addReportAction("step-counter.refresh");
return $appContent->getReport();
//#section_end#
?>