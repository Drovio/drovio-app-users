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

// Import APP Packages
application::import("Security");
//#section_end#
//#section#[view]
use \APP\Security\privateAppKey;
use \APP\Security\publicAppKey;
use \UI\Apps\APPContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

if (engine::isPost())
{
	// Create error notification
	$errorFormNtf = new formErrorNotification();
	$errorFormNtf->build();
	
	// Validate form and create new key
	if (!simpleForm::validate())
	{
		$errorMessage = $errorFormNtf->getMessage("error", "err.save_error");
		$errorFormNtf->append($errorMessage);
		return $errorFormNtf->getReport();
	}
	
	// Get key type to create
	$keyType = engine::getVar("key_type");
	$status = FALSE;
	switch ($keyType)
	{
		case "public":
			$status = publicAppKey::create();
			break;
		case "private":
			$status = privateAppKey::create();
			break;
	}
	
	// Check status
	if (!$status)
		return $errorFormNtf->getReport();
	
	// Reload key list
	$appContent->addReportAction("settings.keys.list.reload");
	
	// Return output
	return $appContent->getReport();
}

// Return output
return $appContent->getReport();
//#section_end#
?>