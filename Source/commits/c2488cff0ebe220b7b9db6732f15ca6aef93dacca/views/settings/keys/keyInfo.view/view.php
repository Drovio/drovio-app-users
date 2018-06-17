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
importer::import("UI", "Apps");
importer::import("UI", "Forms");

// Import APP Packages
//#section_end#
//#section#[view]
use \AEL\Security\publicKey;
use \UI\Apps\APPContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "keyInfoContainer", TRUE);

// Get key
$akey = engine::getVar("akey");
if (engine::isPost())
{
	// Create error notification
	$errorFormNtf = new formErrorNotification();
	$errorFormNtf->build();
	
	// Validate form and create new key
	if (!simpleForm::validate())
	{
		$errorMessage = $errorFormNtf->getMessage("error", "err.save_error");
		$errorFormNtf->append($errorMessage);
		return $errorFormNtf->getReport();
	}
	
	// Regenerate key and get new key to reference
	$akey = publicKey::regenerateKey($akey);
	if (!$akey)
	{
		return $errorFormNtf->getReport();
	}
	
	// Set action to refresh keys
	$appContent->addReportAction($name = "settings.keys.list.reload");
}

// Get key information
$keyInfo = publicKey::info($akey);

$holder = HTML::select(".keyInfo .kr.akey .value")->item(0);
HTML::innerHTML($holder, $keyInfo['akey']);

$holder = HTML::select(".keyInfo .kr.type .value")->item(0);
HTML::innerHTML($holder, $keyInfo['type_name']);

$holder = HTML::select(".keyInfo .kr.date .value")->item(0);
HTML::innerHTML($holder, date("M d, Y, H:i:s", $keyInfo['time_created']));

// Check for extra information
if (!empty($keyInfo['previous_akey']))
{
	$holder = HTML::select(".keyInfo .kr.previous_akey .value")->item(0);
	HTML::innerHTML($holder, $keyInfo['previous_akey']);

	$holder = HTML::select(".keyInfo .kr.exdate .value")->item(0);
	HTML::innerHTML($holder, date("M d, Y, H:i:s", $keyInfo['time_expires']));
}
else
{
	$kr = HTML::select(".keyInfo .kr.previous_akey")->item(0);
	HTML::remove($kr);
	$kr = HTML::select(".keyInfo .kr.exdate")->item(0);
	HTML::remove($kr);
}



// Set key actions
$actionsContainer = HTML::select(".keyInfo .keyActions")->item(0);

// Remove key form
$form = new simpleForm();
$removeForm = $form->build("", FALSE)->engageApp("settings/keys/removeKey")->get();
HTML::addClass($removeForm, "actionForm");
HTML::append($actionsContainer, $removeForm);

// key value hidden input
$input = $form->getInput($type = "hidden", $name = "akey", $value = $akey, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

// Submit button
$title = $appContent->getLiteral("settings.keys.info", "lbl_removeKey");
$removeKey = $form->getSubmitButton($title);
HTML::addClass($removeKey, "key_action remove");
$form->append($removeKey);


// Regenerate key form
$form = new simpleForm();
$regenerateForm = $form->build("", FALSE)->engageApp("settings/keys/keyInfo")->get();
HTML::addClass($regenerateForm, "actionForm");
HTML::append($actionsContainer, $regenerateForm);

// key value hidden input
$input = $form->getInput($type = "hidden", $name = "akey", $value = $akey, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

// Submit button
$title = $appContent->getLiteral("settings.keys.info", "lbl_regenerateKey");
$removeKey = $form->getSubmitButton($title);
HTML::addClass($removeKey, "key_action regenerate");
$form->append($removeKey);



// Return output
return $appContent->getReport("#keyInfoContainer", APPContent::REPLACE_METHOD);
//#section_end#
?>