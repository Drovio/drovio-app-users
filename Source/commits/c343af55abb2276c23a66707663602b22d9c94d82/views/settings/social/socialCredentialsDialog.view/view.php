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
application::import("Resources");
application::import("Utils");
//#section_end#
//#section#[view]
use \UI\Apps\APPContent;
use \UI\Presentation\frames\dialogFrame;
use \UI\Forms\formReport\formNotification;
use \APP\Utils\DayLogger;
use \APP\Utils\UserLogger;
use \APP\Resources\settings;
use \APP\Resources\socialLogin;

if (engine::isPost())
{	// expecting type, enabled, and if enabled client_secret, client_id, scope and redirect_uri
	$type = $_POST['type'];
	$client_id = $_POST['client_id'];
	$client_secret = $_POST['client_secret'];
	$scope = $_POST['scope'];
	$redirect_after_signin = $_POST['redirect_after_signin'];
	$storingFields = new socialLogin($type);
	$storingFields->setup($client_id, $client_secret, $scope, $redirect_after_signin);
	
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
$frame->build($title = $socialType." API cedentials", $action = "", $background = TRUE, $type = dialogFrame::TYPE_OK_CANCEL)->engageApp("settings/social/socialCredentialsDialog");
$form = $frame->getFormFactory();

// Add guide html
$appContent = new APPContent();
$appContent->build("", "guideContainer", TRUE);
$redirectUriElem = HTML::select(".guide .instructions .redirect_uri")->item(0);
HTML::innerHTML($redirectUriElem, $socialLoginInfo->getRedirectUri());
switch ($socialType) {
	case "facebook" :
		$appSetupUrl = "https://developers.facebook.com/docs/apps/register";
		break;
	case "google" :
		$appSetupUrl = "https://developers.google.com/identity/sign-in/web/devconsole-project";
		break;
	default :
		$appSetupUrl = false;
}

$link = HTML::select(".step2 .link")->item(0);
$link->setAttribute("href", $appSetupUrl);
HTML::innerHTML($link, $socialType);
$frame->append($appContent->get());

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

// Scope
$input = $form->getInput($type = "text", $name = "scope", $value = $socialLoginInfo->get("scope"), $class = "", $autofocus = FALSE, $required = FALSE);
$frow = $form->buildRow("Scope", $input, $required = FALSE, $notes = "Provide the scope fields for authorization.");
$form->append($frow);

// Redirect uri
$input = $form->getInput($type = "text", $name = "redirect_after_signin", $value = $socialLoginInfo->get("redirect_after_signin"), $class = "", $autofocus = FALSE, $required = FALSE);
$frow = $form->buildRow("After Signin Redirect URL", $input, $required = FALSE, $notes = "This is where we will redirect your users after they signin in using ".$socialType);
$form->append($frow);

return $frame->getFrame();
//#section_end#
?>