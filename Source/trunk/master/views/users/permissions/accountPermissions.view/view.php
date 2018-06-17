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
importer::import("AEL", "Identity");
importer::import("DRVC", "Security");
importer::import("UI", "Apps");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");

// Import APP Packages
//#section_end#
//#section#[view]
use \AEL\Identity\permissions;
use \UI\Apps\APPContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Presentation\dataGridList;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "accountPermissionsContainer", TRUE);

// Get all user groups
$allGroups = permissions::getInstance()->getAllGroups();

// Get account user groups
$accountID = engine::getVar("aid");
$accountGroups = permissions::getInstance()->getAccountGroups($accountID);
if (engine::isPost())
{
	// Activate or deactivate user groups
	foreach ($allGroups as $groupID => $groupName)
	{
		// Check to activate
		if (!isset($accountGroups[$groupID]) && isset($_POST['gid'][$groupID]))
			permissions::getInstance()->addAccountGroup($accountID, $groupID);
		
		if (isset($accountGroups[$groupID]) && !isset($_POST['gid'][$groupID]))
			permissions::getInstance()->removeAccountGroup($accountID, $groupID);
	}
	
	// Check to create a new group
	if (!empty($_POST['new_group']))
	{
		// Create new group
		permissions::getInstance()->addGroup($_POST['new_group']);
		
		// Refresh all groups
		$allGroups = permissions::getInstance()->getAllGroups();
		$newGroupID = array_search($_POST['new_group'], $allGroups);
		permissions::getInstance()->addAccountGroup($accountID, $newGroupID);
	}
	
	$formNtf = new formNotification();
	$formNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE, $disposable = TRUE);
	$formNtf->addReportAction($name = "users.details.permissions.reload");
	return $formNtf->getReport($fullReset = FALSE);
}


if (!permissions::getInstance()->status())
{
	$p = DOM::create("p", "Account permissions haven't been activated.");
	$appContent->append($p);
}
else
{
	$form = new simpleForm();
	$pForm = $form->build()->engageApp("users/permissions/accountPermissions")->get();
	$appContent->append($pForm);
	
	$input = $form->getInput($type = "hidden", $name = "aid", $value = $accountID, $class = "", $autofocus = FALSE, $required = FALSE);
	$form->append($input);
	
	// Build grid list
	$gridList = new dataGridList();
	$groupList = $gridList->build($id = "", $checkable = TRUE)->get();
	$form->append($groupList);

	// Set headers
	$headers = array();
	$headers[] = "Group ID";
	$headers[] = "Group Name";
	$gridList->setHeaders($headers);
	foreach ($allGroups as $groupID => $groupName)
	{
		// Create the row to insert into the list
		$row = array();

		// Add info into the row
		$row[] = "".$groupID;
		$row[] = $groupName;

		// Insert the row
		$gridList->insertRow($row, $checkName = "gid[".$groupID."]", $checked = isset($accountGroups[$groupID]), $checkValue = $groupID);
	}
	
	// Create new group
	$input = $form->getInput($type = "text", $name = "new_group", $value = "", $class = "", $autofocus = FALSE, $required = FALSE);
	$form->insertRow("New group", $input, $required = FALSE, $notes = "Create a new group and add the account");
}

// Return output
return $appContent->getReport();
//#section_end#
?>