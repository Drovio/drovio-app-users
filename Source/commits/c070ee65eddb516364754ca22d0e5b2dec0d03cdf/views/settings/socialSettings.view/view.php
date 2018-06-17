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
use \UI\Forms\formReport\formNotification;

if (engine::isPost()) {
	$settings = new settings();
	if ($_POST['facebook-authenticate'] == 'on') {
		$value = 1;
	} else {
		$value = 0;
	}
	$settings->set('facebook-authenticate', $value);
	if ($_POST['google-authenticate'] == 'on') {
		$value = 1;
	} else {
		$value = 0;
	}
	$settings->set('google-authenticate', $value);
	$settings->update();
	$allSettings = $settings->get();
	$formNotification = new formNotification();
	$formNotification->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE, $disposable=FALSE);
	$formNotification->appendCustomMessage("Setting saved succesfully.");
	return $formNotification->getReport($fullReset = FALSE);
}

// Get settings
$settings = new settings();
$fbAuthentication = $settings->get('facebook-authenticate');
$gglAuthentication = $settings->get('google-authenticate');

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "social-login-settings-container", TRUE);

// Create form
$form = new simpleForm();
$formElement = $form->build($action = "settings/socialSettings", TRUE, FALSE)->get();
$form->engageApp("settings/socialSettings");

// Create form rows
$input = $form->getInput($type = "checkbox", $name = "facebook-authenticate", $value = "on", $class = "", $autofocus = FALSE, $required = FALSE);
if ($fbAuthentication == 1) {
	$input->setAttribute("checked", TRUE);
}
$frow = $form->buildRow("Facebook", $input, $required = FALSE, $notes = "");
$form->append($frow);

$input = $form->getInput($type = "checkbox", $name = "google-authenticate", $value = "on", $class = "", $autofocus = FALSE, $required = FALSE);
if ($gglAuthentication == 1) {
	$input->setAttribute("checked", TRUE);
}
$frow = $form->buildRow("Google", $input, $required = FALSE, $notes = "");
$form->append($frow);

$appContent->append($formElement);

// Edit buttons 
$submitButton = HTML::select(".formControls .simpleFormRow .uiFormButton")->item(0);
DOM::innerHTML($submitButton, "<span>Save changes</span>");
$resetButton = HTML::select(".formControls .simpleFormRow .uiFormButton")->item(1);
DOM::innerHTML($resetButton, "<span>Cancel</span>");

// Return output
return $appContent->getReport();

//#section_end#
?>