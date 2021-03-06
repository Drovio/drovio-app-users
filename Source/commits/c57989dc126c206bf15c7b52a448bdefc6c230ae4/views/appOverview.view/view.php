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
importer::import("API", "Profile");
importer::import("DRVC", "Profile");
importer::import("UI", "Apps");

// Import APP Packages
//#section_end#
//#section#[view]
use \API\Profile\team;
use \DRVC\Profile\account;
use \UI\Apps\APPContent;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "appOverviewContainer", TRUE);

// Get all accounts
$teamName = team::getTeamUName();
$teamName = strtolower($teamName);
account::init($teamName);
$teamAccounts = account::getAllAccounts();

// Get user count
$ql_title = HTML::select(".appOverview .qlinks .ql.users .title")->item(0);
$attr = array();
$attr['ucount'] = count($teamAccounts);
$title = $appContent->getLiteral("overview", "lbl_ql_users", $attr);
DOM::append($ql_title, $title);

// Return output
return $appContent->getReport();
//#section_end#
?>