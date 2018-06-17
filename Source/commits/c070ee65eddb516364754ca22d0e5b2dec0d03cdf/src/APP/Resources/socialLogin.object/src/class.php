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

use \AEL\Resources\appSettings;

class socialLogin extends appSettings
{	
	private $loginType;
	
	// Constructor Method
	public function __construct($type)
	{
		// Put your constructor method code here.
		$loginType = $type;
		$filename = $type."loginSettings";
		parent::__construct($mode = appSettings::TEAM_MODE, $shared = FALSE, $settingsFolder = "/social_login/", $filename);
		
	}
	
	public function setup($client_id, $client_secret, $redirect_uri) 
	{
		$this->set('client_id', $client_id);
		$this->set('client_secret', $client_secret);
		$this->set('redirect_uri', $redirect_uri);
	}
	
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
}
//#section_end#
?>