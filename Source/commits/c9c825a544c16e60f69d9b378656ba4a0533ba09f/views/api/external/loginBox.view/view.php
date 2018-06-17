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
importer::import("UI", "Content");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");

// Import APP Packages
application::import("Identity");
application::import("Security");
//#section_end#
//#section#[view]
use \APP\Security\publicAppKey;
use \APP\Identity\account;
use \UI\Content\JSONContent;
use \UI\Apps\APPContent;
use \UI\Forms\templates\simpleForm;
use \UI\Presentation\popups\popup;

// Create json content
$jsonContent = new JSONContent();

// Get host origin and public key
$publicKey = publicAppKey::getAPIKey();

// Get the current public key (for test only)
$tester = FALSE;
if (empty($publicKey))
{
	$tester = TRUE;
	$keys = publicAppKey::getTeamKeys();
	$publicKey = array_shift($keys)['akey'];
}

// Create login dialog
$appContent = new APPContent();
$appContent->build("", "identity-login-box-container", TRUE);


// Build login form
$formContainer = HTML::select(".box-main.login .formContainer")->item(0);
$form = new simpleForm($id = "identity-loginbox-form-login");
$loginForm = $form->build($action = "https://api.drov.io/dev/apps/89/".$publicKey."/api/login", $defaultButtons = FALSE, $async = TRUE)->get();
DOM::append($formContainer, $loginForm);

// Input container
$inpContainer = DOM::create("div", "", "", "inp-container");
$form->append($inpContainer);

// Username
$input = $form->getInput($type = "text", $name = "username", $value = "", $class = "lpinp", $autofocus = TRUE, $required = TRUE);
DOM::attr($input, "placeholder", "Email or username");
DOM::append($inpContainer, $input);

// Password
$input = $form->getInput($type = "password", $name = "password", $value = "", $class = "lpinp", $autofocus = FALSE, $required = TRUE);
DOM::attr($input, "placeholder", "Password");
DOM::append($inpContainer, $input);

// Login button
$input = $form->getSubmitButton($title = "Login", $id = "", $name = "", $class = "lpbtn");
$form->append($input);



// Build registration form
$formContainer = HTML::select(".box-main.register .formContainer")->item(0);
$form = new simpleForm($id = "identity-loginbox-form-register");
$registerForm = $form->build($action = "https://api.drov.io/dev/apps/89/".$publicKey."/api/register", $defaultButtons = FALSE, $async = TRUE)->get();
DOM::append($formContainer, $registerForm);

// Login indicator
$input = $form->getInput($type = "hidden", $name = "login", $value = 1, $class = "lpinp", $autofocus = TRUE, $required = TRUE);
$form->append($input);

// Input container
$inpContainer = DOM::create("div", "", "", "inp-container");
$form->append($inpContainer);

// Name
$input = $form->getInput($type = "text", $name = "full_name", $value = "", $class = "lpinp", $autofocus = TRUE, $required = TRUE);
DOM::attr($input, "placeholder", "Full name");
DOM::append($inpContainer, $input);

// Email
$input = $form->getInput($type = "email", $name = "email", $value = "", $class = "lpinp", $autofocus = TRUE, $required = TRUE);
DOM::attr($input, "placeholder", "Email");
DOM::append($inpContainer, $input);

// Password
$input = $form->getInput($type = "password", $name = "password", $value = "", $class = "lpinp", $autofocus = FALSE, $required = TRUE);
DOM::attr($input, "placeholder", "Password");
DOM::append($inpContainer, $input);

// Login button
$input = $form->getSubmitButton($title = "Sign Up", $id = "", $name = "", $class = "lpbtn");
$form->append($input);



// Build password recovery form
$formContainer = HTML::select(".box-main.recover .formContainer")->item(0);
$form = new simpleForm($id = "identity-loginbox-form-recover");
$recoverForm = $form->build($action = "https://api.drov.io/dev/apps/89/".$publicKey."/api/resetPassword", $defaultButtons = FALSE, $async = TRUE)->get();
DOM::append($formContainer, $recoverForm);

// Input container
$inpContainer = DOM::create("div", "", "", "inp-container");
$form->append($inpContainer);

// Email
$input = $form->getInput($type = "email", $name = "email", $value = "", $class = "lpinp", $autofocus = TRUE, $required = TRUE);
DOM::attr($input, "placeholder", "Recovery email");
DOM::append($inpContainer, $input);

// Login button
$input = $form->getSubmitButton($title = "Recover", $id = "", $name = "", $class = "lpbtn");
$form->append($input);


// Build password reset form
$formContainer = HTML::select(".box-main.reset .formContainer")->item(0);
$form = new simpleForm($id = "identity-loginbox-form-reset");
$recoverForm = $form->build($action = "https://api.drov.io/dev/apps/89/".$publicKey."/api/account/updatePasswordFromReset", $defaultButtons = FALSE, $async = TRUE)->get();
DOM::append($formContainer, $recoverForm);

// Input container
$inpContainer = DOM::create("div", "", "", "inp-container");
$form->append($inpContainer);

// Reset token
$input = $form->getInput($type = "text", $name = "reset_id", $value = "", $class = "lpinp", $autofocus = TRUE, $required = TRUE);
DOM::attr($input, "placeholder", "Reset token");
DOM::append($inpContainer, $input);

// Password
$input = $form->getInput($type = "password", $name = "password", $value = "", $class = "lpinp", $autofocus = FALSE, $required = TRUE);
DOM::attr($input, "placeholder", "Password");
DOM::append($inpContainer, $input);

// Confirm Password
$input = $form->getInput($type = "password", $name = "password_confirm", $value = "", $class = "lpinp", $autofocus = FALSE, $required = TRUE);
DOM::attr($input, "placeholder", "Re-type password");
DOM::append($inpContainer, $input);

// Login button
$input = $form->getSubmitButton($title = "Reset", $id = "", $name = "", $class = "lpbtn");
$form->append($input);



// Build all social logins



// Copy social logins to sign up form
$signUpSocialContainer = HTML::select(".box-main.register .sc-container")->item(0);
$social = HTML::select(".box-main.login .social")->item(0)->cloneNode(TRUE);
DOM::prepend($signUpSocialContainer, $social);


// check if it's tester and show popup
if ($tester)
{
	// Create popup
	$pp = new popup();
	$pp->type($type = popup::TP_OBEDIENT, $toggle = FALSE);
	$pp->background(TRUE);

	// Build and get report
	return $pp->build($appContent->get())->getReport();
}

// Return output
return $appContent->getReport($holder = "", $method = APPContent::REPLACE_METHOD, $allowOrigin = "", $withCredentials = TRUE);
//#section_end#
?>