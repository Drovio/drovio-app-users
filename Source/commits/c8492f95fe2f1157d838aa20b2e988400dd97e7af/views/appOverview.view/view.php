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

$d1count = HTML::select(".dactivity .dact.d1 .count")->item(0);
$count = number_format(DayLogger::getTodaySignupCount());
DOM::innerHTML($d1count, $count);

$d7count = HTML::select(".dactivity .dact.d7 .count")->item(0);
$count = number_format(DayLogger::getLastNDaysSignupCount(7));
DOM::innerHTML($d7count, $count);

$d30count = HTML::select(".dactivity .dact.d30 .count")->item(0);
$count = number_format(DayLogger::getLastNDaysSignupCount(30));
DOM::innerHTML($d30count, $count);

// Setup Quick Links
// Get accounts count
$accountCount = account::getInstance()->getAccountsCount();

// Get user count
$ql_title = HTML::select(".appOverview .qlinks .ql.users .title")->item(0);
$attr = array();
$attr['ucount'] = number_format($accountCount);
$title = $appContent->getLiteral("overview", "lbl_ql_users", $attr);
DOM::append($ql_title, $title);

// Return output
return $appContent->getReport();
//#section_end#
?>