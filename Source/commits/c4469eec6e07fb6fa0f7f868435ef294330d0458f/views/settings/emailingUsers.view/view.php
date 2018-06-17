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
importer::import("UI", "Forms");

// Import APP Packages
application::import("Resources");
//#section_end#
//#section#[view]
use \APP\Resources\settings;
use \UI\Apps\APPContent;
use \UI\Forms\templates\simpleForm;

if (engine::isPost()) {
	$settings = new settings();
	if ($_POST['send-password-update-confirmation-email'] == true) {
		$value = 1;
	} else {
		$value = 0;
	}
	$settings->set('send-password-update-confirmation-email', $value);
	if ($_POST['welcome-email'] == true) {
		$value = 1;
	} else {
		$value = 0;
	}
	$settings->set('welcome-email', $value, settings::SCOPE_USER);
	$settings->update();
}

// Get settings
$settings = new settings();
$allSettings = $settings->get();
if (array_key_exists('SEND-PASSWORD-UPDATE-CONFIRMATION-EMAIL', $allSettings)) {
	$pwdUpdateConfirm = $allSettings['SEND-PASSWORD-UPDATE-CONFIRMATION-EMAIL'];
} else {
	$pwdUpdateConfirm = 1;
}
if (array_key_exists('SEND-WELCOME-EMAIL', $allSettings)) {
	$pwdUpdateConfirm = $allSettings['SEND-WELCOME-EMAIL'];
} else {
	$welcomeEmail = 1;
}

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "emailing-users-settingsContainer", TRUE);

//$formContainer = HTML::select(".emailing-users-settings")->item(0);
// Create form    //TO-DELETE: 2 tick boxes + 1 "Save Changes" button
$form = new simpleForm();
$formElement = $form->build($action = "settings/emailingUsers", TRUE, FALSE)->get();
$form->engageApp("settings/emailingUsers");

// Create form rows
$input = $form->getInput($type = "checkbox", $name = "send-password-update-confirmation-email", $value = "", $class = "", $autofocus = FALSE, $required = FALSE);
if ($pwdUpdateConfirm) {
	$input->setAttribute("checked", "TRUE");
} else {
	$input->setAttribute("checked", "FALSE");
}
$frow = $form->buildRow("Send password update confirmation", $input, $required = FALSE, $notes = "");
$form->append($frow);
$input = $form->getInput($type = "checkbox", $name = "send-welcome-email", $value = "", $class = "", $autofocus = FALSE, $required = FALSE);
if ($welcomeEmail) {
	$input->setAttribute("checked", "TRUE");
} else {
	$input->setAttribute("checked", "FALSE");
}
$frow = $form->buildRow("Send welcome email", $input, $required = FALSE, $notes = "");
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