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
importer::import("DRVC", "Security");
importer::import("UI", "Apps");
importer::import("UI", "Forms");

// Import APP Packages
//#section_end#
//#section#[view]
use \UI\Apps\APPContent;
use \UI\Forms\templates\simpleForm;
use \DRVC\Security\permissions;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

if (engine::isPost())
{
	// Activate permissions
	$status = permissions::activate();
	if (!$status)
	{
		$appContent->addReportAction($name = "settings.permissions.notification", $value = "Error creating the permissions feature.");
		return $appContent->getReport(".nowhere");
	}
	
	// Reload permissions
	$appContent->addReportAction($name = "settings.permissions.reload", $value);
	return $appContent->getReport(".nowhere");
}

// Build the application view content
$appContent->build("", "pmSettingsContainer", TRUE);

// Check permissions status
if (permissions::status())
{
	// Remove inactive container
	$inactive = HTML::select(".status.inactive")->item(0);
	HTML::remove($inactive);
}
else
{
	// Remove active container
	$active = HTML::select(".status.active")->item(0);
	HTML::remove($active);
	
	// Create form to activate
	$formContainer = HTML::select(".status.inactive .pFormContainer")->item(0);
	$form = new simpleForm();
	$pForm = $form->build("", FALSE)->engageApp("settings/pmSettings")->get();
	DOM::append($formContainer, $pForm);
	
	// Create submit button
	$submit = $form->getSubmitButton($title = "Enable permissions", $id = "", $name = "", $class = "pbutton");
	$form->append($submit);
}

// Return output
return $appContent->getReport();
//#section_end#
?>