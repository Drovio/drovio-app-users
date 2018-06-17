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
/**
 * @library	APP
 * @package	Mail
 * 
 * @copyright	Copyright (C) 2015 DrovioUserManagement. All rights reserved.
 */

importer::import("AEL", "Mail", "appMailer");
importer::import("AEL", "Resources", "resource");
application::import("Resources", "settings");

use \AEL\Mail\appMailer;
use \AEL\Resources\resource;
use \APP\Resources\settings;

/**
 * Application mailer
 * 
 * Sends emails to users for specific actions.
 * 
 * @version	0.1-1
 * @created	October 26, 2015, 17:21 (GMT)
 * @updated	October 26, 2015, 17:21 (GMT)
 */
class appMail
{
	/**
	 * Send password recovery directions to the user.
	 * 
	 * @param	string	$email
	 * 		The user's email.
	 * 
	 * @param	string	$resetID
	 * 		The reset id token.
	 * 
	 * @return	void
	 */
	public static function sendResetPasswordMail($email, $resetID)
	{
		// Get the reset page url from the settings
		$settings = new settings();
		$resetURL = $settings->get("reset_password_page_url");
		
		// Set reset url
		$resetURL .= "?reset_id=".$resetID;
		
		// Set resetUrl attribute
		$attr = array();
		$attr['reset_token'] = $resetID;
		$attr['reset_url'] = $resetURL;
		
		// Send email
		return self::sendTemplateMail($email, "Password Recovery", "/mail/resetPasswordTemplate.txt", "/mail/resetPasswordTemplate.html", $attr);
	}
	
	/**
	 * Send a confirmation email for updating the user's password.
	 * 
	 * @param	string	$email
	 * 		The user's email.
	 * 
	 * @return	void
	 */
	public static function sendPasswordUpdatedConfirmation($email)
	{
		// Send email
		return self::sendTemplateMail($email, "Password Update Confirmation", "/mail/updatePasswordConfirmation.txt");
	}
	
	/**
	 * Send a welcome email to the user.
	 * 
	 * @param	string	$email
	 * 		The user's email.
	 * 
	 * @return	void
	 */
	public static function sendWelcomeEmail($email)
	{
		// Send email
		return self::sendTemplateMail($email, "Welcome", "/mail/welcomeEmail.txt");
	}
	
	/**
	 * Send an email from the templates.
	 * 
	 * @param	string	$recipient
	 * 		The recipient's email address.
	 * 
	 * @param	string	$subject
	 * 		The email subject.
	 * 
	 * @param	string	$templatePath_text
	 * 		The path to the email text content.
	 * 
	 * @param	string	$templatePath_html
	 * 		The path to the email html content.
	 * 
	 * @param	array	$attr
	 * 		An array of attributes for the email body.
	 * 		$attr['attr_name'] = [attr_value].
	 * 
	 * @return	void
	 */
	private static function sendTemplateMail($recipient, $subject, $templatePath_text, $templatePath_html, $attr = array())
	{
		// GET EMAIL CONTENT
		
		// Load email template
		$mailContentsTEXT = resource::get($templatePath_text);
		$mailContentsHTML = resource::get($templatePath_html);
		
		// Set attributes
		foreach ($attr as $attrName => $attrValue)
		{
			$mailContentsTEXT = str_replace("&{".$attrName."}", $attrValue, $mailContentsTEXT);
			$mailContentsHTML = str_replace("&{".$attrName."}", $attrValue, $mailContentsHTML);
		}
		
		
		// SEND EMAIL
		
		// Create and send email
		$appMailer = new appMailer(appMailer::MODE_TEAM);
		
		// Add recipient
		$appMailer->addRecipient($recipient);

		// Send the email
		return $appMailer->send($subject, $mailContentsTEXT, $mailContentsHTML);
	}
}
//#section_end#
?>