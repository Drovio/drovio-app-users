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
application::import("Resources");
application::import("Utils");
//#section_end#
//#section#[view]
use \APP\Identity\account;
use \UI\Presentation\frames\dialogFrame;
use \UI\Forms\formReport\formNotification;
use \APP\Utils\DayLogger;
use \APP\Utils\UserLogger;
use \APP\Resources\settings;
use \APP\Resources\socialLogin;

account::init();
if (engine::isPost())
{	// expecting type, enabled, and if enabled client_secret, client_id, redirect_uri
	$type = $_POST['type'];
	$client_id = $_POST['client_id'];
	$client_secret = $_POST['client_secret'];
	$redirect_uri = $_POST['redirect_uri'];
	
	$storingFields = new socialLogin($type);
	$storingFields->setup($client_id, $client_secret, $redirect_uri);
	
	// Prepare sucess message
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = FALSE);
	
	// Notification Message
	$succMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($succMessage);
	// Log event
	DayLogger::log("sociallogin", $type." enabled");
	return $succFormNtf->getReport(FALSE);
}

$socialType = $_GET['type'];
$settings = new settings();
if ($settings->get($type.'-authenticate') == '1') {
	$enabled = true;
} else {
	$enabled = false;
}
$socialLoginInfo = new socialLogin($socialType);

$frame = new dialogFrame();
$frame->build($title = $socialType." API cedentials", $action = "", $background = TRUE, $type = dialogFrame::TYPE_OK_CANCEL)->engageApp("settings/socialCredentialsDialog");
$form = $frame->getFormFactory();

//Hidden row with type
$input = $form->getInput($type = "text", $name = "type", $value = $socialType, $class = "", $autofocus = FALSE, $required = FALSE);
$frow = $form->buildRow("", $input, $required = FALSE, $notes = "");
$frow->setAttribute("style", "display:none;");
$form->append($frow);

// Client id
$input = $form->getInput($type = "text", $name = "client_id", $value = $socialLoginInfo->get("client_id"), $class = "", $autofocus = FALSE, $required = FALSE);
$frow = $form->buildRow("Client ID", $input, $required = FALSE, $notes = "");
$form->append($frow);

// Client secret
$input = $form->getInput($type = "text", $name = "client_secret", $value = $socialLoginInfo->get("client_secret"), $class = "", $autofocus = FALSE, $required = FALSE);
$frow = $form->buildRow("Client Secret", $input, $required = FALSE, $notes = "");
$form->append($frow);

// Redirect uri
$input = $form->getInput($type = "text", $name = "redirect_uri", $value = $socialLoginInfo->get("redirect_uri"), $class = "", $autofocus = FALSE, $required = FALSE);
$frow = $form->buildRow("Redirect URL", $input, $required = FALSE, $notes = "");
$form->append($frow);

return $frame->getFrame();
//#section_end#
?>