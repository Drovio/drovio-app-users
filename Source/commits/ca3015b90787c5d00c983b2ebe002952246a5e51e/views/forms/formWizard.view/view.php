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
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");

// Import APP Packages
application::import("Resources");
application::import("Security");
//#section_end#
//#section#[view]
use \AEL\Resources\resource;
use \APP\Security\appKey;
use \APP\Resources\settings;
use \APP\Resources\socialLogin;
use \UI\Apps\APPContent;
use \UI\Developer\editors\HTML5Editor;
use \UI\Developer\codeMirror;
use \UI\Forms\Form;
use \UI\Presentation\tabControl;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Get form name
$formName = engine::getVar("fname");

// Build the application view content
$appContent->build("", "formWizardContainer", TRUE);

// Load form html template
$formHTMLTemplate = resource::get("/forms/".$formName.".form.template.html");
$loginTypes = array("facebook", "google");
$settings = new settings();
foreach ($loginTypes as $loginType) {
	// get button if login type enabled
	if ($settings->get($loginType.'-authenticate') == '1') {
		$enabled = TRUE;
	} else {
		$enabled = FALSE;
	}
	if ($enabled) {
		// Prepare button and add to form
		$loginButton = resource::get("/forms/social_buttons/".$loginType."-login-button.html");
		// Get client ID
		$socialLoginInfo = new socialLogin($loginType);
		$client_id = $socialLoginInfo->get("client_id");
		$fbSettings = new socialLogin($loginType);
		$redirect_uri = $fbSettings->getRedirectUri();
		$loginButton = str_replace("%{client_id}", $client_id, $loginButton);
		$loginButton = str_replace("%{redirect_uri}", $redirect_uri, $loginButton);
		$loginButton = str_replace("%{path-to-buttons}", resource::getURL("/forms/social_buttons"), $loginButton);
	} else {
		$loginButton = "";
	}
	$formHTMLTemplate = str_replace("%{".$loginType."-login-button}", $loginButton, $formHTMLTemplate);
}

// Create html editor
$htmlEditor = new HTML5Editor($htmlName = "htmlEditor", $enablePreview = TRUE);
$htmlFormEditor = $htmlEditor->build($formHTMLTemplate, $id = "formHTMLEditor", $class = "formHTMLEditor")->get();
$step1Container_body = HTML::select(".formWizard .step.html_sample .step__body")->item(0);
HTML::prepend($step1Container_body, $htmlFormEditor);

// Get team keys
$publicKeys = appKey::getTeamKeys();
$selectedKey = $publicKeys[0]['akey'];

// All supporting languages
$languages = array();
$languages['PHP'] = codeMirror::PHP;
$languages['Node.js'] = codeMirror::JS;
//$languages['Ruby'] = codeMirror::NO_PARSER;
//$languages['Go'] = codeMirror::NO_PARSER;
//$languages['Python'] = codeMirror::NO_PARSER;

// All language extensions
$extensions = array();
$extensions['PHP'] = "php";
$extensions['Node.js'] = "js";
$extensions['Ruby'] = "rb";
$extensions['Go'] = "go";
$extensions['Python'] = "py";

// Create form factory
$form = new Form();

// List backend code for all languages
$selected = TRUE;
$languageContainer = HTML::select(".formWizard .step.api .step__body .languageContainer")->item(0);
$codeEditorContainer = HTML::select(".formWizard .step.api .step__body .editorContainer")->item(0);
foreach ($languages as $lName => $codeType)
{
	// Get code template
	$backendCodeTemplate = resource::get("/forms/backend/".$lName."/".$formName.".backend.".$extensions[$lName].".template");
	// Replace key
	$backendCodeTemplate = str_replace("%{akey}", $selectedKey, $backendCodeTemplate);
	
	// Add radio button for each language
	$lItem = DOM::create("div", "", "", "lItem ".$extensions[$lName]);
	DOM::data($lItem, "lng", strtolower($lName));
	if ($selected)
		HTML::addClass($lItem, "selected");
	DOM::append($languageContainer, $lItem);
	$radioButton = $form->getInput($type = "radio", $name = "lbackend", $value = strtolower($lName), $class = "rhidden", $autofocus = FALSE, $required = FALSE);
	if ($selected)
		HTML::attr($radioButton, "checked", TRUE);
	DOM::append($lItem, $radioButton);
	$radioID = DOM::attr($radioButton, "id");
	$llabel = $form->getLabel($text = $lName, $for = $radioID, $class = "llabel");
	DOM::append($lItem, $llabel);
	
	// Set static nav
	$refID = "ref_".$extensions[$lName];
	$appContent->setStaticNav($lItem, $refID, $targetcontainer = "backendContainer", $targetgroup = "bgroup", $navgroup = "bnavgroup", $display = "none");
	
	// Insert code mirror editor for the code
	$editorGroup = DOM::create("div", "", $refID, "editorGroup");
	DOM::append($codeEditorContainer, $editorGroup);
	$appContent->setNavigationGroup($editorGroup, "bgroup");
	// Build code mirror
	$cm = new codeMirror($codeType, $name = "backend_".strtolower($lName), $readOnly = FALSE);
	$codeEditor = $cm->build($backendCodeTemplate, $id = $refID."_cm", $class = "cmEditor")->get();
	DOM::append($editorGroup, $codeEditor);
	
	// Add buttons
	$continueButton = DOM::create("div", "Continue", "", "wbutton continue");
	DOM::append($editorGroup, $continueButton);
	
	$cm_textarea = HTML::select("#".$refID."_cm textarea")->item(0);
	$cm_textarea_id = HTML::attr($cm_textarea, "id");
	$copyButton = DOM::create("div", "Copy this backend code", "copy-api-".strtolower($lName), "wbutton copy");
	DOM::data($copyButton, "clipboard-target", $cm_textarea_id);
	DOM::data($copyButton, "clipboard-text", "Error at copying the code.");
	DOM::append($editorGroup, $copyButton);
	
	$copyLabel = DOM::create("div", "Backend code copied to clipboard...", "", "copylabel copy-api-label");
	DOM::append($editorGroup, $copyLabel);
	
	$selected = FALSE;
}

// Check if keys are empty
if (!empty($publicKeys))
{
	$ntf = HTML::select("h2.ntf")->item(0);
	DOM::remove($ntf);
}

// Remove 'next form' button if it's the last form
if ($formName == 'recover')
{
	$nextButton = HTML::select(".formWizard .wbutton.next_form")->item(0);
	HTML::remove($nextButton);
}

// Activate copy button
$appContent->addReportAction("formwizard.activate_copy");

// Return output
return $appContent->getReport();
//#section_end#
?>