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
use \APP\Resources\settings;
use \UI\Apps\APPContent;
use \UI\Forms\templates\simpleForm;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

if (engine::isPost())
{
	// Build success response
	$appContent->build("", "appPricingContainer", TRUE);
	
	// Remove step container
	$stepContainer = HTML::select(".appPricing .stepContainer")->item(0);
	HTML::remove($stepContainer);
	
	// Complete setup
	$settings = new settings();
	$setupCompleted = $settings->set("setup_completed", 1);
	
	// Show buttons and return report
	$appContent->addReportAction("pricing.show_step_buttons");
	return $appContent->getReport("", "replace");
}

// Build the application view content
$appContent->build("", "appPricingContainer", TRUE);

// Remove success container
$successContainer = HTML::select(".appPricing .successContainer")->item(0);
HTML::remove($successContainer);

// Set finish action
$btnFinish = HTML::select(".step-btn.next.finish")->item(0);
$actionFactory->setAction($btnFinish, "dashboard/TeamExplorer", ".applicationDashboardContainer");

// Create initial settings form
$formContainer = HTML::select(".appPricing .formContainer")->item(0);
$form = new simpleForm();
$settingsForm = $form->build("", FALSE)->engageApp("dashboard/setup/pricingPlan")->get();
HTML::append($formContainer, $settingsForm);

$submit = $form->getSubmitButton($title = "Enable Pricing Plan", $id = "", $name = "", $class = "fbutton");
$form->append($submit);

// Return output
return $appContent->getReport();
//#section_end#
?>