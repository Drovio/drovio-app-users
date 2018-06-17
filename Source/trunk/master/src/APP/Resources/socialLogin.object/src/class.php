<?php
//#section#[header]
// Namespace
namespace APP\Resources;

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
 * @package	Resources
 * 
 * @copyright	Copyright (C) 2015 DrovioUserManagement. All rights reserved.
 */

importer::import("AEL", "Resources", "appSettings");
importer::import("AEL", "Security", "publicKey");

use \AEL\Resources\appSettings;
use \AEL\Security\publicKey;

/**
 * socialLogin
 * 
 * Class that helps manage the settings for the supported social authentications.
 * 
 * @version	2.1-1
 * @created	November 20, 2015, 19:22 (GMT)
 * @updated	December 1, 2015, 19:29 (GMT)
 */
class socialLogin extends appSettings
{
	/**
	 * which social login is the current object setting
	 * 
	 * @type	string
	 */
	private $loginType;
	
	/**
	 * Creates the object and sets the social network the object handles.
	 * 
	 * @param	string	$type
	 * 		The social network for which the object handles settings.
	 * 		Accepted values:
	 * 		- facebook
	 * 		- google
	 * 		- github
	 * 		- twitter
	 * 		- linkedin
	 * 		- windows
	 * 
	 * @return	void
	 */
	public function __construct($type)
	{
		// Put your constructor method code here.
		$this->loginType = $type;
		$filename = $type."loginSettings";
		parent::__construct($mode = appSettings::TEAM_MODE, $shared = FALSE, $settingsFolder = "/social_login/", $filename);
	}
	
	public function status($enabled = NULL)
	{
		// Update enabled status
		if (!is_null($enabled))
			$this->set('enabled', ($enabled ? 1 : 0));
		
		// Return status
		return $this->get("enabled");
	}
	
	/**
	 * Sets all the required parameters for the login to function.
	 * 
	 * @param	string	$client_id
	 * 		The OAuth client id from the provider.
	 * 
	 * @param	string	$client_secret
	 * 		The OAuth app secret from the provider.
	 * 
	 * @param	string	$scope
	 * 		The scope attribute to request authorization for.
	 * 
	 * @param	string	$redirect_after_signin
	 * 		URL where to redirect users after they successfully sign in.
	 * 
	 * @return	void
	 */
	public function setup($client_id, $client_secret, $scope, $redirect_after_signin) 
	{
		// Set normal credentials
		$this->set('client_id', $client_id);
		$this->set('client_secret', $client_secret);
		$this->set('scope', $scope);
		$this->set('redirect_after_signin', $redirect_after_signin);
		
		// Set base url
		switch ($this->loginType)
		{
			case "facebook":
				$baseURL = "https://www.facebook.com/dialog/oauth";
				break;
			case "google":
				$baseURL = "https://accounts.google.com/o/oauth2/auth";
				break;
			case "github":
				$baseURL = "https://github.com/login/oauth/authorize";
				break;
		}
		$this->set('base_url', $baseURL);
	}
	
	/**
	 * Get a value from the settings.
	 * 
	 * @param	string	$key
	 * 		Key for required setting.
	 * 
	 * @return	mixed
	 * 		Returns the settings value or an array with all keys in lowercase.
	 */
	public function get($key = "")
	{
		// Get normal value
		$value = parent::get($key);
		
		// Transform to lowercase array if empty key
		if (empty($key))
		{
			$lowercaseKeyArray = array();
			foreach ($response as $key => $value)
				$lowercaseKeyArray[strtolower($key)] = $value;
			
			return $lowercaseKeyArray;
		}
		
		// Return normal response
		return $value;
	}
	
	/**
	 * Get the redirect uri that the user must set for the application.
	 * 
	 * @return	string
	 * 		The redirect uri after a successful social login.
	 */
	public function getRedirectUri()
	{
		$publicKey = NULL;
		$appKeys = publicKey::getTeamKeys();
		foreach ($appKeys as $key)
			if ($key['type_name'] == 'APP_PUBLIC_KEY')
			{
				$publicKey = $key['akey'];
				break;
			}
		
		// Create new public 
		if (empty($publicKey))
			$publicKey = publicKey::create();
		
		// Check if public key exists
		if ($publicKey == FALSE)
			return NULL;
		
		// Get redirect uri
		$appId = application::init();
		return "https://api.drov.io/apps/".$appId."/".$publicKey."/api/social/".$this->loginType."Login";
	}
		
}
//#section_end#
?>