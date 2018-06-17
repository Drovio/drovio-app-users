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
//#section_end#
//#section#[view]
use \APP\Identity\account;
use \UI\Apps\APPContent;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "settings_billingInfoContainer", TRUE);

// Get team users count
account::init();
$userCount = account::getInstance()->getAccountsCount();

// Set info limit
$maximumUsers = 1000;
$remainingUsers = $maximumUsers - $userCount;
$attr = array();
$attr['rusers'] = ($remainingUsers < 0 ? 0 : $remainingUsers);
$title = $appContent->getLiteral("settings.billing", "lbl_usersLimit", $attr);
$infoHolder = HTML::select(".billingInfo .plan_row.free .info")->item(0);
HTML::append($infoHolder, $title);

// Set bar width
$bar = HTML::select(".billingInfo .plan_row.free .bar")->item(0);
$width = (($userCount > $maximumUsers ? $maximumUsers : $userCount) / $maximumUsers) * 100;
HTML::style($bar, "width", $width."%");

// Return output
return $appContent->getReport();
//#section_end#
?>