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
importer::import("AEL", "Security");
importer::import("API", "Profile");
importer::import("DRVC", "Comm");
importer::import("DRVC", "Profile");
importer::import("UI", "Apps");
importer::import("UI", "Forms");

// Import APP Packages
application::import("Resources");
//#section_end#
//#section#[view]
use \AEL\Security\publicKey;
use \AEL\Security\privateKey;
use \API\Profile\team;
use \DRVC\Profile\identity;
use \UI\Apps\APPContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \APP\Resources\settings;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "startScreenContainer", TRUE);

if (engine::isPost())
{
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// validate form
	if (!simpleForm::validate())
		return $errFormNtf->getReport();
	
	// Identity setup database
	$status = identity::setup($_POST['tname'], $_POST['pwd']);
	if (!$status)
	{
		$err_header = $appContent->getLiteral("sscreen", "lbl_createdb_error");
		$err = $errFormNtf->addHeader($err_header);
		$desc = $appContent->getLiteral("sscreen", "lbl_createdb_error_desc");
		$errFormNtf->addDescription($err, $desc);
		return $errFormNtf->getReport();
	}
	
	// Create a new private and public key
	privateKey::create();
	publicKey::create();
	
	// Set settings
	$settings = new settings();
	
	// Set demo password
	$settings->set("demo_pwd", $_POST['pwd']);
	
	// Set initial settings
	$settings->set("send-welcome-email", 0);
	$settings->set("send-password-update-confirmation-email", 0);
	$settings->set("send-name-update-confirmation-email", 0);
	$settings->set("send-username-update-confirmation-email", 0);
	
	// Return success notification and load main view
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = FALSE);
	
	// Load main view
	$mView = $appContent->loadView("MainView");
	$succFormNtf->addReportContent($mView, $holder = ".drovioUserManagementApplication", $method = "replace");
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Create form
$formContainer = HTML::select(".startScreen .formContainer")->item(0);
$form = new simpleForm();
$setupForm = $form->build("", FALSE)->engageApp("StartScreen")->get();
DOM::append($formContainer, $setupForm);

// Set password
$password = substr(hash("SHA256", "identity_".time()."_".mt_rand()), 0, 16);
$input = $form->getInput($type = "hidden", $name = "pwd", $value = $password, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

// Set team name
$teamName = team::getTeamUName();
$input = $form->getInput($type = "hidden", $name = "tname", $value = $teamName, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

// Set literals
$dbName = HTML::select(".startScreen p.sub.db_name")->item(0);
$attr = array();
$attr['tname'] = $teamName;
$title = $appContent->getLiteral("sscreen", "lbl_dbName", $attr);
DOM::append($dbName, $title);

// Create submit button
$title = $appContent->getLiteral("sscreen", "lbl_setup");
$button = $form->getSubmitButton($title, $id = "setup_button", $name = "");
$form->append($button);

// Return output
return $appContent->getReport();
//#section_end#
?>