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
importer::import("UI", "Forms");
importer::import("UI", "Presentation");

// Import APP Packages
application::import("Identity");
application::import("Mail");
//#section_end#
//#section#[view]
use \APP\Identity\account;
use \APP\Mail\appMail;
use \DRVC\Profile\accountSession;
use \UI\Apps\APPContent;
use \UI\Presentation\popups\popup;
use \UI\Forms\Form;
use \UI\Forms\formReport\formNotification;
use \API\Profile\team;


if (engine::isPost()) {
	$formNtf = new formNotification();
	if ($_POST["form-type"] == "reset-password") {
		$resetID = account::getInstance()->generateResetId($_POST['aid']);
		if ($resetID != FALSE) {
			$formNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = FALSE);
			$formNtf->appendCustomMessage("Reset token has been successfully generated.");
			$account = account::getInstance()->info($_POST['aid']);
			appMail::sendResetPasswordMail($account['mail'], $resetID);
		} else {		
			$formNtf->build($type = formNotification::ERROR, $header = TRUE, $timeout = FALSE, $disposable = FALSE);
			$formNtf->appendCustomMessage("Failed to generate reset password token. Please try again.");
		}
	} else if ($_POST["form-type"] == "delete-user") {
		account::init();
		$account = account::info($_POST['aid']);
		if ($account['mail'] == "demo@identity.drov.io") {
			$formNtf->build($type = formNotification::ERROR, $header = TRUE, $timeout = FALSE, $disposable = FALSE);
			$formNtf->appendCustomMessage("You cannot delete the demo account.");
		} else {
			$accountDeleted = account::getInstance()->removeAccount($_POST['aid']);
			if ($accountDeleted != FALSE) {
				$formNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = FALSE);
				$formNtf->appendCustomMessage("User has been successfully removed");
			} else {
				$formNtf->build($type = formNotification::ERROR, $header = TRUE, $timeout = FALSE, $disposable = FALSE);
				$formNtf->appendCustomMessage("Failed to remove user. Please try again.");
			}
		}
	}
	return $formNtf->getReport();
}

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "userDetailsContainer", TRUE);

// Set profile image

//TODO: get aid and use it to retrieve user profile
$account = account::getInstance()->info($_GET['aid']);

// Set profile information
$info = HTML::select(".basic-info ")->item(0);
$firstname = HTML::select('.basic-info .value')->item(0);
HTML::innerHTML($firstname, $account['title']);
$username = HTML::select('.basic-info .value')->item(1);
HTML::innerHTML($username, $account['username']);
$email = HTML::select('.basic-info .value')->item(2);
HTML::innerHTML($email, $account['mail']);

// Get last session information
$activeSessionsContainer = HTML::select(".active-sessions")->item(0);
// Get team name and accountSession object
$teamName = strtolower(team::getTeamUName());
$accSession = accountSession::getInstance($teamName);
$activeSessions = $accSession->getActiveSessions($_GET['aid']);
if ($activeSessions != NULL && count($activeSessions) > 0) {
	foreach ($activeSessions as $session) {
		$infoRow = HTML::create("div", "", "", "row");
		$ip = HTML::create("div", $session["ip"], "", "info-cell");
		$lastUpdate = HTML::create("div", date('d M y', $session["lastAccess"]), "", "info-cell");
		$deviceType = HTML::create("div", $session["userAgent"], "", "info-cell");
		$deviceType->setAttribute("title", $session["userAgent"]);
		$endSession = HTML::create("div",'End session', "", "info-cell");
		HTML::append($infoRow, $ip);
		HTML::append($infoRow, $lastUpdate);
		HTML::append($infoRow, $deviceType);
		HTML::append($infoRow, $endSession);
		HTML::append($activeSessionsContainer, $infoRow);
		
	}
} else {
	$headerRow = HTML::select(".active-sessions .headers")->item(0);
	HTML::remove($headerRow);
	$infoRow = HTML::create("div", "There are no active sessions for this account.", "", "row");
	HTML::append($activeSessionsContainer, $infoRow);
	
}
	
	

// Create action forms
$formContainer = $info = HTML::select(".actions ")->item(0);
$form = new Form();

// Build forms
$profileForm = $form->build($action = "users/userDetails", $async = TRUE, $fileUpload = FALSE)->get();
$form->engageApp("users/userDetails");

$profileForm->setAttribute("class", "user-action-form");

$input = $form->getInput($type = "hidden", $name = "aid", $value = $_GET['aid'], $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

// Add radio buttons
$input = $form->getInput($type = "radio", $name = "form-type", $value = "reset-password", $class = "", $autofocus = FALSE, $required = FALSE);
$input->setAttribute("style", "display:none");
$input->setAttribute("id", "useraction1");
$form->append($input);
$input = $form->getInput($type = "radio", $name = "form-type", $value = "delete-user", $class = "", $autofocus = FALSE, $required = FALSE);
$input->setAttribute("style", "display:none");
$input->setAttribute("id", "useraction2");
$form->append($input);

// Add submit buttons
$submitButton = $form->getSubmitButton("Reset Password", "reset-password-button", "Reset password");
$submitButton->setAttribute("class", "button");
$submitButton->setAttribute("onclick", "document.getElementById('useraction1').checked=true; jq('.user-action-form').trigger('submit');");
$form->append($submitButton);
$submitButton = $form->getSubmitButton("Delete account", "delete-account-button", "Delete account");
$submitButton->setAttribute("class", "button");
$submitButton->setAttribute("onclick", "document.getElementById('useraction2').checked=true; jq('.user-action-form').trigger('submit');");
$form->append($submitButton);

// Append form to form container
DOM::append($formContainer, $profileForm);

// Create popup
$pp = new popup();
$pp->type($type = popup::TP_OBEDIENT, $toggle = FALSE);
$pp->background(TRUE);
$pp->build($appContent->get());

return $pp->getReport();
//#section_end#
?>