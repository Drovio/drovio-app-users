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
importer::import("UI", "Presentation");

// Import APP Packages
//#section_end#
//#section#[view]
use \AEL\Security\appKey;
use \UI\Apps\APPContent;
use \UI\Forms\templates\simpleForm;
use \UI\Presentation\dataGridList;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "settings_appKeysContainer", TRUE);
$appKeys = HTML::select(".appKeys")->item(0);

// Get all application keys as a team
$teamKeys = appKey::getTeamKeys();
if (!empty($teamKeys))
{
	// Empty list
	$list = HTML::select(".appKeys .list")->item(0);
	HTML::innerHTML($list, "");
	
	// Create data grid list
	$gridList = new dataGridList();
	$keysList = $gridList->build($id = "keysList", $checkable = FALSE, $withBorder = TRUE)->get();
	DOM::append($list, $keysList);
	
	// Set column ratios
	$ratios = array();
	$ratios['type'] = 0.2;
	$ratios['date'] = 0.2;
	$ratios['akey'] = 0.5;
	$ratios['actions'] = 0.1;
	$gridList->setColumnRatios($ratios);
	
	// Set headers
	$headers = array();
	$headers['type'] = "Type";
	$headers['date'] = "Date Created";
	$headers['akey'] = "API Key";
	$headers['actions'] = "Actions";
	$gridList->setHeaders($headers);
	
	// Show all keys
	foreach ($teamKeys as $keyInfo)
	{
		// Key row
		$row = array();
		$row['type'] = $keyInfo['type_name'];
		$row['date'] = date('M d, Y', $keyInfo['time_created']);
		$row['akey'] = $keyInfo['akey'];
		
		// Create action container
		$actionContainer = DOM::create("div", "", "", "keyActionContainer");
		
		// Edit action (show popup)
		$editKey = DOM::create("div", "", "", "act edit");
		DOM::append($actionContainer, $editKey);
		
		// Set edit action
		$attr = array();
		$attr['akey'] = $keyInfo['akey'];
		$actionFactory->setAction($editKey, "settings/keys/editKeyDialog", "", $attr);
		
		// Remove key form
		$form = new simpleForm();
		$removeKeyForm = $form->build("", FALSE)->engageApp("settings/keys/removeKey")->get();
		HTML::addClass($removeKeyForm, "keyForm");
		DOM::append($actionContainer, $removeKeyForm);
		
		// key value hidden input
		$input = $form->getInput($type = "hidden", $name = "akey", $value = $keyInfo['akey'], $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		// key type hidden input
		$input = $form->getInput($type = "hidden", $name = "key_type", $value = $keyInfo['type_id'], $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		// Submit button
		$removeKey = $form->getSubmitButton();
		HTML::addClass($removeKey, "act remove");
		$form->append($removeKey);
		
		$row['action'] = $actionContainer;
		
		// Insert row
		$gridList->insertRow($row);
	}
}
else
{
	// Create form for adding new key
	$form = new simpleForm();
	$newKeyForm = $form->build("", FALSE)->engageApp("settings/keys/createNewKey")->get();
	DOM::append($appKeys, $newKeyForm);

	// Set add_key action
	$addUserButton = HTML::select(".appKeys .newKeyButton")->item(0);
	$form->append($addUserButton);
}

// Activate control action
$controls = HTML::select(".appKeys .controls")->item(0);

// Create form for adding new public key
$form = new simpleForm();
$newKeyForm = $form->build("", FALSE)->engageApp("settings/keys/createNewKey")->get();
DOM::append($controls, $newKeyForm);

$input = $form->getInput($type = "hidden", $name = "key_type", $value = "public", $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

// Add submit button
$title = $appContent->getLiteral("settings.keys.list", "lbl_newPublicKey");
$submitButton = $form->getSubmitButton($title);
HTML::addClass($submitButton, "ctrl add_key");
$form->append($submitButton);

// Create form for adding new private key
$form = new simpleForm();
$newKeyForm = $form->build("", FALSE)->engageApp("settings/keys/createNewKey")->get();
DOM::append($controls, $newKeyForm);

$input = $form->getInput($type = "hidden", $name = "key_type", $value = "private", $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

// Add submit button
$title = $appContent->getLiteral("settings.keys.list", "lbl_newPrivateKey");
$submitButton = $form->getSubmitButton($title);
HTML::addClass($submitButton, "ctrl add_key");
$form->append($submitButton);

// Return output
return $appContent->getReport();
//#section_end#
?>