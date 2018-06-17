<?php
//#section#[header]
// Namespace
namespace APP\Mail;

require_once($_SERVER['DOCUMENT_ROOT'].'/_domainConfig.php');

// Use Important Headers
use \API\Platform\importer;
use \Exception;

// Check Platform Existance
if (!defined('_RB_PLATFORM_')) throw new Exception("Platform is not defined!");

// Import application loader
importer::import("AEL", "Platform", "application");
use \AEL\Platform\application;
//#section_end#
//#section#[class]
importer::import("AEL", "Mail", "appMailer");
importer::import("AEL", "Resources", "resource");
application::import("Resources", "settings");

use \AEL\Mail\appMailer;
use \AEL\Resources\resource;
use \APP\Resources\settings;

class appMail
{
	public static function sendResetPasswordMail($email, $resetID)
	{
		// GET EMAIL CONTENT
		
		// Load email template
		$mailContentsTEXT = resource::get("/mail/resetPasswordTemplate.txt");
		$mailContentsHTML = resource::get("/mail/resetPasswordTemplate.html");
		
		// Get the reset page url from the settings
		$settings = new settings();
		$resetURL = $settings->get("reset_password_page_url");
		
		// Set reset url
		$resetURL .= "?reset_id=".$resetID;
		
		// Replace url to mail content
		$mailContentsTEXT = str_replace("&{reset_url}", $resetURL, $mailContentsTEXT);
		$mailContentsHTML = str_replace("&{reset_url}", $resetURL, $mailContentsHTML);
		
		
		// SEND EMAIL
		
		// Create and send email
		$appMailer = new appMailer(appMailer::MODE_TEAM);
		
		// Add recipient
		$appMailer->addRecipient($email);

		// Send the email
		return $appMailer->send("Password Recovery", $mailContentsTEXT, $mailContentsHTML);
	}
	
	public static function sendPasswordUpdatedConfirmation($email)
	{
		// GET EMAIL CONTENT
		
		// Load email template
		$mailContentsTEXT = resource::get("/mail/updatePasswordConfirmation.txt");
		
		// SEND EMAIL
		
		// Create and send email
		$appMailer = new appMailer(appMailer::MODE_TEAM);
		
		// Add recipient
		$appMailer->addRecipient($email);

		// Send the email
		return $appMailer->send("Password Update Confirmation", $mailContentsTEXT);
	}
	
	public static function sendWelcomeEmail($email)
	{
		// GET EMAIL CONTENT
		
		// Load email template
		$mailContentsTEXT = resource::get("/mail/welcomeEmail.txt");
		
		// SEND EMAIL
		
		// Create and send email
		$appMailer = new appMailer(appMailer::MODE_TEAM);
		
		// Add recipient
		$appMailer->addRecipient($email);

		// Send the email
		return $appMailer->send("Password Update Confirmation", $mailContentsTEXT);
	}
}
//#section_end#
?>