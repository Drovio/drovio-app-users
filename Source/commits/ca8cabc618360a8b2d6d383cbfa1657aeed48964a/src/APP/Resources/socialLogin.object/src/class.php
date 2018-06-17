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
 * @version	1.0-2
 * @created	November 20, 2015, 19:22 (GMT)
 * @updated	November 22, 2015, 19:41 (GMT)
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
	
	/**
	 * Sets all the required parameters for the login to function.
	 * 
	 * @param	string	$client_id
	 * 		App id.
	 * 
	 * @param	string	$client_secret
	 * 		App secret
	 * 
	 * @param	string	$redirect_after_signin
	 * 		URL where to redirect users after they successfully sign in.
	 * 
	 * @return	void
	 */
	public function setup($client_id, $client_secret, $redirect_after_signin) 
	{
		$this->set('client_id', $client_id);
		$this->set('client_secret', $client_secret);
		$this->set('redirect_after_signin', $redirect_after_signin);
	}
	
	/**
	 * Value stored for given key.
	 * 
	 * @param	string	$key
	 * 		Key for required setting.
	 * 
	 * @return	string
	 * 		{description}
	 */
	public function get($key = "") {
		$response = parent::get($key);
		if ($key == NULL) {
			$lowercaseKeyArray = array();
			foreach ($response as $key => $value) {
				$lowercaseKeyArray[strtolower($key)] = $value;
			}
			return $lowercaseKeyArray;
		}
		return $response;
	}
	
	/**
	 * Creates the redirect uri the user must set for the application.
	 * 
	 * @return	string
	 * 		URL user must set as redirect uri when creating application for social login.
	 */
	public function getRedirectUri() {
		$appKeys = publicKey::getTeamKeys();
		$publicKey = NULL; 
		foreach ($appKeys as $key) {
			if ($key['type_name'] == 'APP_PUBLIC_KEY') {
				$publicKey = $key['akey'];
				break;
			}
		}
		if (empty($publicKey)) {
			$publicKey = publicKey::create();
		}
		if ($publicKey == false) {
			// Throw error
		}
		$appId = application::init();
		return "https://api.drov.io/apps/".$appId."/".$publicKey."/api/social/".$this->loginType."Login";
	}
		
}
//#section_end#
?>