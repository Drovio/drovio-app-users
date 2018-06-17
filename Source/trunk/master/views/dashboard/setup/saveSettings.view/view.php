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
application::import("Resources");
//#section_end#
//#section#[view]
use \APP\Resources\settings;
use \UI\Apps\APPContent;
use \UI\Forms\templates\simpleForm;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

if (engine::isPost())
{
	// Update settings
	$stArray = array();
	$stArray[] = "send-welcome-email";
	$stArray[] = "send-password-update-confirmation-email";
	
	$settings = new settings();
	foreach ($stArray as $stName)
		$settings->set($stName, ($_POST[$stName] ? 1 : 0));
	
	// Update settings
	$settings->update();
	
	// Build success response
	$appContent->build("", "appSettingsContainer", TRUE);
	
	// Remove step container
	$stepContainer = HTML::select(".appSettings .stepContainer")->item(0);
	HTML::remove($stepContainer);
	
	// Show buttons and return report
	$appContent->addReportAction("settings.show_step_buttons");
	return $appContent->getReport("", "replace");
}

// Build success response
$appContent->build("", "appSettingsContainer", TRUE);

// Remove success container
$successContainer = HTML::select(".appSettings .successContainer")->item(0);
HTML::remove($successContainer);

// Create initial settings form
$formContainer = HTML::select(".appSettings .formContainer")->item(0);
$form = new simpleForm();
$settingsForm = $form->build("", FALSE)->engageApp("dashboard/setup/saveSettings")->get();
HTML::append($formContainer, $settingsForm);

// Get settings
$settings = new settings();
$allSettings = $settings->get();
$welcomeEmail = $settings->get('SEND-WELCOME-EMAIL');
$pwdUpdateConfirm = $settings->get('SEND-PASSWORD-UPDATE-CONFIRMATION-EMAIL');

// Create form rows
insertSettingsRow($form, $title = "Registration email", $name = "send-welcome-email", $checked = ($welcomeEmail == 1 || !isset($welcomeEmail)));
insertSettingsRow($form, $title = "Password update notification", $name = "send-password-update-confirmation-email", $checked = ($pwdUpdateConfirm == 1 || !isset($pwdUpdateConfirm)));

$submit = $form->getSubmitButton($title = "Save", $id = "", $name = "", $class = "fbutton");
$form->append($submit);

return $appContent->getReport();

function insertSettingsRow($form, $title, $name, $checked = FALSE)
{
	$input = $form->getInput($type = "checkbox", $name, $value = "", $class = "", $autofocus = FALSE, $required = FALSE);
	if ($checked)
		DOM::attr($input, "checked", "checked");
	$form->insertRow($title, $input, $required = FALSE, $notes = "");
}
//#section_end#
?>