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
importer::import("DRVC", "Profile");
importer::import("UI", "Apps");
importer::import("UI", "Presentation");

// Import APP Packages
application::import("Identity");
//#section_end#
//#section#[view]
use \APP\Identity\account;
use \UI\Apps\APPContent;
use \UI\Presentation\dataGridList;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "userListContainer", TRUE);

// Get total accounts
$accountsCount = account::getInstance()->getAccountsCount();

// Get page attributes
$pageCount = engine::getVar("count");
$pageCount = (empty($pageCount) || $pageCount < 0 ? 50 : $pageCount);
$pageIndex = engine::getVar("page");
$pageIndex = ($pageIndex < 0 ? 0 : $pageIndex);
$pageIndex = ($pageIndex > ceil($accountsCount / $pageCount) ? ceil($accountsCount / $pageCount) : $pageIndex);

//var_dump($pageIndex);
//var_dump($pageCount);
// Get team users
$teamAccounts = account::getInstance()->getAllAccounts($pageIndex * $pageCount, $pageCount);

// Set user count
$attr = array();
$attr['ucount'] = number_format($accountsCount, 0);
$hd = HTML::select(".userList .listContainer h1.hd")->item(0);
$title = $appContent->getLiteral("users.list", "lbl_userCount", $attr);
DOM::append($hd, $title);

// Set pagination text
$attr = array();
$attr['start'] = $pageIndex * $pageCount + 1;
$attr['end'] = ($accountsCount < ($pageIndex + 1) * $pageCount ? $accountsCount : ($pageIndex + 1) * $pageCount);
$attr['total'] = $accountsCount;
$text = HTML::select(".userList .pagination .pg-text")->item(0);
$title = $appContent->getLiteral("users.list", "lbl_pagination_title", $attr);
DOM::append($text, $title);

// Set pagingation buttons actions
$pg_nextButton = HTML::select(".userList .pagination .pg-btn.next")->item(0);
if ($pageIndex < ceil($accountsCount / $pageCount) - 1)
{
	$attr = array();
	$attr['page'] = $pageIndex + 1;
	$attr['count'] = $pageCount;
	$actionFactory->setAction($pg_nextButton, $viewName = "users/userList", "#dusm_ref_users", $attr, $loading = TRUE);
}
else
	HTML::addClass($pg_nextButton, "disabled");

$pg_prevButton = HTML::select(".userList .pagination .pg-btn.previous")->item(0);
if ($pageIndex > 0)
{
	$attr = array();
	$attr['page'] = $pageIndex - 1;
	$attr['count'] = $pageCount;
	$actionFactory->setAction($pg_prevButton, $viewName = "users/userList", "#dusm_ref_users", $attr, $loading = TRUE);
}
else
	HTML::addClass($pg_prevButton, "disabled");

// Build data grid list
$listContainer = HTML::select(".userList .list")->item(0);
$gridList = new dataGridList();
$userList = $gridList->build($id = "", $checkable = TRUE, $withBorder = TRUE)->get();
HTML::addClass($userList, "uList");
DOM::append($listContainer, $userList);

// Set ratios
$ratios = array();
$ratios['id'] = 0.08;
$ratios['utitle'] = 0.30;
$ratios['username'] = 0.25;
$ratios['umail'] = 0.30;
$ratios['uaction'] = 0.07;
$gridList->setColumnRatios($ratios);

// Set headers
$headers = array();
$headers['id'] = $appContent->getLiteral("users.list", "hd_uid");
$headers['utitle'] = $appContent->getLiteral("users.list", "hd_utitle");
$headers['username'] = $appContent->getLiteral("users.list", "hd_username");
$headers['umail'] = $appContent->getLiteral("users.list", "hd_umail");
$headers['uaction'] = "...";
$gridList->setHeaders($headers);

// List all users
foreach ($teamAccounts as $userInfo)
{
	$row = array();
	$row['id'] = $userInfo['id'];
	$row['utitle'] = $userInfo['title'];
	$row['username'] = $userInfo['username'];
	$row['umail'] = $userInfo['mail'];
	
	// Set action
	$uaction = DOM::create("div", "", "", "fld uaction");
	$row['uaction'] = $uaction;
	
	// Set details action
	$attr = array();
	$attr['aid'] = $userInfo['id'];
	$actionFactory->setAction($uaction, "users/userDetails", "", $attr);
	
	// Insert row
	$gridList->insertRow($row);
}

if (empty($teamAccounts))
{
	// Remove list container
	$listContainer = HTML::select(".userList .listContainer .list")->item(0);
	HTML::replace($listContainer, NULL);
	
	// Set add_user action
	$addUserButton = HTML::select(".userList .no_users .qlink.add")->item(0);
	$actionFactory->setAction($addUserButton, "users/addUserDialog", "", True);
}
else
{
	// Remove no user notification
	$no_users = HTML::select(".userList .no_users")->item(0);
	HTML::replace($no_users, NULL);
}

// Set add_user action
$addUserButton = HTML::select(".userList .listContainer .action.add")->item(0);
$actionFactory->setAction($addUserButton, "users/addUserDialog");

// Return output
return $appContent->getReport();
//#section_end#
?>