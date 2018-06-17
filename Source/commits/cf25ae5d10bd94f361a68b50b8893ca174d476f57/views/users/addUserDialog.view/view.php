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
application::import("Mail");
application::import("Utils");
//#section_end#
//#section#[view]
use \APP\Identity\account;
use \APP\Mail\appMail;
use \UI\Presentation\frames\dialogFrame;
use \UI\Forms\formReport\formNotification;
use \APP\Utils\DayLogger;
use \APP\Utils\UserLogger;

account::init();
if (engine::isPost())
{
	// Create user
	if ($_POST['upass'] != $_POST['upass_confirm'])
	{
		// Show error notification
		$errorFormNtf = new formNotification();
		$errorFormNtf->build($type = formNotification::ERROR, $header = TRUE, $timeout = FALSE, $disposable = FALSE);
		$errorMessage = $errorFormNtf->getMessage("error", "err.save_error");
		$errorFormNtf->append($errorMessage);
		return $errorFormNtf->getReport();
	}
	
	// Create account
	$status = account::create($email = $_POST['umail'], $firstname = $_POST['first_name'], $lastname = $_POST['last_name'], $password = $_POST['upass']);
	
	if ($status == false) {
		
		// Show failed submission notification
		$errorFormNtf = new formNotification();
		$errorFormNtf->build($type = formNotification::ERROR, $header = TRUE, $timeout = FALSE, $disposable = FALSE);
		$errorMessage = $errorFormNtf->getMessage("error", "err.invalid_data");
		$errorFormNtf->append($errorMessage);
		return $errorFormNtf->getReport();
	}
	
	// (on success $status is the account id)
	$aid = $status;
	$accountInfo = account::getAPIInstance()->info($aid);
	if ($_POST['action'] == 'welcome-email') {
		// Send welcome email
		appMail::sendWelcomeEmail($accountInfo['mail'], $resetID);
	} else if ($_POST['action'] == 'welcome-update-password') {
		// Reset password & send email
		$resetID = account::getAPIInstance()->generateResetId($aid);
		appMail::sendResetPasswordMail($accountInfo['mail'], $resetID);
		
	}
		
	// Return success notification and load main view
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = FALSE);

	// Refresh user list
	$succFormNtf->addReportAction($name = "users.list.reload");
	
	// Notification Message
	$succMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($succMessage);
	
	
	// Log event
	DayLogger::log("created-user", $_POST['umail']." has been registered via the 'Add user' dialog in User list.");
	UserLogger::log($accountInfo['id'], "created-user", $_POST['umail']." has been registered via the 'Add user' dialog in User list.");
	return $succFormNtf->getReport();
}


$frame = new dialogFrame();
$frame->build($title = "Add User", $action = "", $background = TRUE, $type = dialogFrame::TYPE_OK_CANCEL)->engageApp("users/addUserDialog");
$form = $frame->getFormFactory();


// First name
$input = $form->getInput($type = "text", $name = "first_name", $value = "", $class = "", $autofocus = TRUE, $required = TRUE);
$frow = $form->buildRow("First name", $input, $required = TRUE, $notes = "");
$form->append($frow);


// Last name
$input = $form->getInput($type = "text", $name = "last_name", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
$frow = $form->buildRow("Last name", $input, $required = TRUE, $notes = "");
$form->append($frow);

// Email
$input = $form->getInput($type = "email", $name = "umail", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
$frow = $form->buildRow("E-mail", $input, $required = TRUE, $notes = "");
$form->append($frow);

// Password
$input = $form->getInput($type = "password", $name = "upass", $value = "", $class = "", $autofocus = FALSE, $required = FALSE);
$frow = $form->buildRow("Password", $input, $required = FALSE, $notes = "");
$form->append($frow);

// Password confirmation
$input = $form->getInput($type = "password", $name = "upass_confirm", $value = "", $class = "", $autofocus = FALSE, $required = FALSE);
$frow = $form->buildRow("Confirm password", $input, $required = FALSE, $notes = "");
$form->append($frow);

// Radio buttons
$input = $form->getInput($type = "radio", $name = "action", $value = "no-email", $class = "radio", $autofocus = TRUE, $required = TRUE);
$input->setAttribute("style", "width: inherit; vertical-align: -webkit-baseline-middle;");
$input->setAttribute("checked", "true");
$frow = $form->buildRow("Don't send any email", $input, $required = TRUE, $notes = "");
$form->append($frow);
$input = $form->getInput($type = "radio", $name = "action", $value = "welcome-email", $class = "radio", $autofocus = FALSE, $required = TRUE);
$input->setAttribute("style", "width: inherit; vertical-align: -webkit-baseline-middle;");
$frow = $form->buildRow("Send welcome email", $input, $required = TRUE, $notes = "");
$form->append($frow);
$input = $form->getInput($type = "radio", $name = "action", $value = "welcome-updade-password-email", $class = "radio", $autofocus = TRUE, $required = TRUE);
$input->setAttribute("style", "width: inherit; vertical-align: -webkit-baseline-middle;");
$frow = $form->buildRow("Send  welcome and update password email", $input, $required = TRUE, $notes = "");
$form->append($frow);

return $frame->getFrame();
//#section_end#
?>