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
importer::import("UI", "Forms");
importer::import("UI", "Presentation");

// Import APP Packages
application::import("Identity");
//#section_end#
//#section#[view]
use \APP\Identity\account;
use \APP\Identity\accountSession;
use \UI\Apps\APPContent;
use \UI\Presentation\dataGridList;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "accountSessionListContainer");

// Get account active sessions
$accountID = engine::getVar("aid");
$activeSessions = accountSession::getInstance()->getActiveSessions($accountID);
if (!empty($activeSessions))
{
	// Build grid list
	$gridList = new dataGridList();
	$sessionList = $gridList->build()->get();
	$appContent->append($sessionList);

	// Set headers
	$headers = array();
	$headers[] = "Location";
	$headers[] = "Last Access";
	$headers[] = "User Agent";
	$headers[] = "Actions";
	$gridList->setHeaders($headers);
	foreach ($activeSessions as $session)
	{
		
		// Grid row
		$row = array();
		$row[] = $session["location"]." (".$session["ip"].")";
		$row[] = date('d M y', $session["lastAccess"]);
		$row[] = $session["userAgent"];
		
		// End session form action
		$row[] = "";
		
		$gridList->insertRow($row);
		
	}
}
else
{
	$p = DOM::create("p", "There are no active sessions");
	$appContent->append($p);
}

// Return output
return $appContent->getReport();
//#section_end#
?>