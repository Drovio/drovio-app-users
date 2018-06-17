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
importer::import("API", "Login");
importer::import("DRVC", "Profile");
importer::import("UI", "Apps");
importer::import("UI", "Forms");

// Import APP Packages
application::import("Resources");
//#section_end#
//#section#[view]
use \API\Login\team;
use \APP\Resources\teamSettings;
use \DRVC\Profile\account;
use \DRVC\Profile\identity;
use \UI\Apps\APPContent;
use \UI\Forms\templates\simpleForm;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

if (engine::isPost())
{
	// Get team name
	$teamName = engine::getVar("tname");
	
	// Get unique name
	$uname = strtolower($teamName);
	$uname = trim($uname, "._ ");
	$uname = str_replace(" ", "_", $uname);
	$uname = str_replace(".", "_", $uname);
	
	// Check if there is a conflict with the team name
	$allTeams = team::getTeamInstance()->getAllTeams();
	foreach ($allTeams as $teamInfo)
		if (strtolower($teamInfo['uname']) == $uname)
		{
			$appContent->addReportAction("app.notification", "The team name already exists. Please try another.");
			return $appContent->getReport(".null-holder");
		}
	
	// Try to create identity database
	$status = identity::setup($uname);
	if (!$status)
	{
		$appContent->addReportAction("app.notification", "The team name already exists. Please try another.");
		return $appContent->getReport(".null-holder");
	}
	
	// Create team
	$status = team::getTeamInstance()->create($uname, $teamName);
	if (!$status)
	{
		$appContent->addReportAction("app.notification", "An error occurred. Please contact with loginBox at hello@loginbox.io");
		return $appContent->getReport(".null-holder");
	}
	
	// Create demo account
	$demoPassword = substr(hash("SHA256", "drovio.identity.".$uname."_".time()."_".mt_rand()), 0, 16);
	account::getInstance($uname)->create($email = "demo@".$uname.".loginbox.io", $firstname = "Demo", $lastname = "Account", $demoPassword);
	
	// Save demo password to settings
	$settings = new teamSettings($teamName);
	$settings->set("demo_pwd", $demoPassword);
	
	// Build success response
	$appContent->build("", "createTeamContainer", TRUE);
	
	// Remove step container
	$stepContainer = HTML::select(".createTeam .stepContainer")->item(0);
	HTML::remove($stepContainer);
	
	// Show buttons and return report
	$appContent->addReportAction("team.show_step_buttons");
	return $appContent->getReport("", "replace");
}

// Build team creator
$appContent->build("", "createTeamContainer", TRUE);

// Remove success container
$successContainer = HTML::select(".createTeam .successContainer")->item(0);
HTML::remove($successContainer);

// Get account teams and select whether to load the create team form
$createTeamForm = HTML::select("form.create_team_form")->item(0);
$accountTeams = team::getTeamInstance()->getAccountTeams();
if (empty($accountTeams))
{
	// Engage team creation form
	$form = new simpleForm();
	$form->engageStaticApp($createTeamForm, "dashboard/setup/createTeam");
}
else
{
	// Remove team creation form
	HTML::replace($createTeamForm, NULL);
	
	// Enable controls
	$create_team_success = HTML::select(".createTeam .stepContainer .create_team_success")->item(0);
	HTML::removeClass($create_team_success, "noDisplay");
	
	$step_buttons = HTML::select(".createTeam .stepContainer .step-buttons")->item(0);
	HTML::removeClass($step_buttons, "noDisplay");
}

return $appContent->getReport();
//#section_end#
?>