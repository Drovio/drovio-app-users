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
//#section_end#
//#section#[view]
use \APP\Identity\account;
use \APP\Mail\appMail;
use \APP\Identity\accountSession;
use \UI\Apps\APPContent;
use \UI\Presentation\popups\popup;
use \UI\Forms\Form;
use \UI\Forms\formReport\formNotification;
use \UI\Presentation\dataGridList;


if (engine::isPost()) {
	$formNtf = new formNotification();
	if ($_POST["form-type"] == "reset-password") {
		$resetID = account::getInstance()->generateResetId($_POST['aid']);
		if ($resetID != FALSE) {
			$formNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE, $disposable = TRUE);
			$formNtf->appendCustomMessage("Reset token has been successfully generated.");
			$account = account::getInstance()->info($_POST['aid']);
			appMail::sendResetPasswordMail($account['mail'], $resetID);
		} else {		
			$formNtf->build($type = formNotification::ERROR, $header = TRUE, $timeout = FALSE, $disposable = FALSE);
			$formNtf->appendCustomMessage("Failed to generate reset password token. Please try again.");
		}
	} else if ($_POST["form-type"] == "delete-user") {
		$account = account::getInstance()->info($_POST['aid']);
		if ($account['mail'] == "demo@identity.drov.io") {
			$formNtf->build($type = formNotification::ERROR, $header = TRUE, $timeout = FALSE, $disposable = TRUE);
			$formNtf->appendCustomMessage("You cannot delete the demo account.");
		} else {
			$accountDeleted = account::getInstance()->removeAccount($_POST['aid']);
			if ($accountDeleted != FALSE) {
				$formNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE, $disposable = TRUE);
				$formNtf->appendCustomMessage("User has been successfully removed");
				
				// Refresh user list
				$formNtf->addReportAction($name = "users.list.reload");
			} else {
				$formNtf->build($type = formNotification::ERROR, $header = TRUE, $timeout = FALSE, $disposable = TRUE);
				$formNtf->appendCustomMessage("Failed to remove user. Please try again.");
			}
		}
	}
	return $formNtf->getReport($fullReset = FALSE);
}

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "userDetailsContainer", TRUE);

// Get account information
$accountID = engine::getVar("aid");
$accountInfo = account::getInstance()->info($accountID);

$value = HTML::select('.basic-info .row.name .value')->item(0);
HTML::innerHTML($value, $accountInfo['title']);

$value = HTML::select('.basic-info .row.email .value')->item(0);
HTML::innerHTML($value, $accountInfo['mail']);

$value = HTML::select('.basic-info .row.username .value')->item(0);
HTML::innerHTML($value, $accountInfo['username']);


// Set navigation
$nav = array();
$nav["sessions"] = "users/accountSessionList";
$nav["permissions"] = "users/permissions/accountPermissions";
$whiteBox = HTML::select(".userDetails .whiteBox")->item(0);
foreach ($nav as $class => $viewName)
{
	$ref = "ref_".$class;
	$navItem = HTML::select(".userDetails .menu .menu_item.".$class)->item(0);
	$appContent->setStaticNav($navItem, $ref, $targetcontainer = "detailsContainer", $targetgroup = "ugroup", $navgroup = "unavgroup", $display = "none");
	
	$attr = array();
	$attr['aid'] = $accountID;
	$viewContainer = $appContent->getAppViewContainer($viewName, $attr, $startup = TRUE, $containerID = $ref, $loading = FALSE, $preload = FALSE);
	DOM::append($whiteBox, $viewContainer);
	$appContent->setNavigationGroup($viewContainer, "ugroup");
}


// Create action forms
$form = new Form();

// Build forms
$actionContainer = HTML::select(".side-info .actions")->item(0);
$profileForm = $form->build($action = "users/userDetails", $async = TRUE, $fileUpload = FALSE)->engageApp("users/userDetails")->get();
DOM::append($actionContainer, $profileForm);

$input = $form->getInput($type = "hidden", $name = "aid", $value = $accountID, $class = "", $autofocus = FALSE, $required = FALSE);
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
$submitButton = $form->getSubmitButton("Reset Password", "reset-password-button", "Reset password", "abutton");
$submitButton->setAttribute("onclick", "document.getElementById('useraction1').checked=true; jq('.user-action-form').trigger('submit');");
$form->append($submitButton);
$submitButton = $form->getSubmitButton("Delete account", "delete-account-button", "Delete account", "abutton");
$submitButton->setAttribute("onclick", "document.getElementById('useraction2').checked=true; jq('.user-action-form').trigger('submit');");
$form->append($submitButton);


// Create popup
$pp = new popup();
$pp->type($type = popup::TP_OBEDIENT, $toggle = FALSE);
$pp->background(TRUE);

// Build and get report
return $pp->build($appContent->get())->getReport();
//#section_end#
?>