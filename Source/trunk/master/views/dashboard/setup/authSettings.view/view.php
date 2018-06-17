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
use \APP\Resources\socialLogin;
use \UI\Apps\APPContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formControls\switchButton;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

$socialOptions = array();
$socialOptions["facebook"] = "Facebook";
$socialOptions["google"] = "Google";
if (engine::isPost())
{
	foreach ($socialOptions as $type => $scName)
	{
		$sl = new socialLogin($type);
		$status = isset($_POST['social'][$type]);
		$active = $sl->status($status);
	}
	/*
	// Update settings
	$stArray = array();
	$stArray[] = "send-welcome-email";
	$stArray[] = "send-password-update-confirmation-email";
	
	$settings = new settings();
	foreach ($stArray as $stName)
		$settings->set($stName, ($_POST[$stName] ? 1 : 0));
	
	// Update settings
	$settings->update();
	*/
	// Build success response
	$appContent->build("", "authSettingsContainer", TRUE);
	
	// Remove step container
	$stepContainer = HTML::select(".authSettings .stepContainer")->item(0);
	HTML::remove($stepContainer);
	
	// Return report
	$appContent->addReportAction("authSettings.show_step_buttons");
	return $appContent->getReport("", "replace");
}

// Build success response
$appContent->build("", "authSettingsContainer", TRUE);

// Remove success container
$successContainer = HTML::select(".authSettings .successContainer")->item(0);
HTML::remove($successContainer);

// Create initial settings form
$formContainer = HTML::select(".authSettings .formContainer")->item(0);
$form = new simpleForm();
$settingsForm = $form->build("", FALSE)->engageApp("dashboard/setup/authSettings")->get();
HTML::append($formContainer, $settingsForm);

// Normal authentication
$box = getAuthBox($form, $title = "Email Authentication", $type = "email", $social = FALSE);
$form->append($box);

// Social logins
$scLogins = DOM::create("div", "", "", "social-logins");
$form->append($scLogins);
foreach ($socialOptions as $type => $title)
{
	$box = getAuthBox($form, $title, $type);
	DOM::append($scLogins, $box);
}

$submit = $form->getSubmitButton($title = "Save", $id = "", $name = "", $class = "fbutton");
$form->append($submit);

return $appContent->getReport();

function getAuthBox($form, $title, $type, $social = TRUE)
{
	// Build social box
	$scBox = DOM::create("div", "", "", "sc-box");
	
	$ico = DOM::create("div", "", "", "sc-ico ".$type);
	DOM::append($scBox, $ico);
	
	$title = DOM::create("div", $title, "", "sc-title");
	DOM::append($scBox, $title);
	
	// Get social login
	if ($social)
	{
		$sl = new socialLogin($type);
		$active = $sl->status();

		$sb = new switchButton();
		$switch = $sb->build($active, $name = "social[".$type."]", $value = 1, $class = "sc-switch")->get();
		DOM::append($scBox, $switch);
	}
	else
	{
		$tick = DOM::create("div", "", "", "sc-tick");
		DOM::append($scBox, $tick);
	}
	
	return $scBox;
}
//#section_end#
?>