<?php
//#section#[header]
// Namespace
namespace APP\Security;

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
 * @package	Security
 * 
 * @copyright	Copyright (C) 2015 DrovioUserManagement. All rights reserved.
 */

importer::import("AEL", "Security", "publicKey");
importer::import("API", "Platform", "engine");

use \AEL\Security\publicKey;
use \API\Platform\engine;

/**
 * Application key validator.
 * 
 * Extends the Application public key interface and can validate the received key from the API request.
 * 
 * @version	1.0-1
 * @created	October 18, 2015, 14:55 (EEST)
 * @updated	October 18, 2015, 16:21 (EEST)
 */
class appKey extends publicKey
{
	/**
	 * Validate whether the request akey is valid for the team it represents.
	 * 
	 * @return	boolean
	 * 		True if valid, false otherwise.
	 */
	public static function validate()
	{
		// Get current key
		$akey = self::getAPIKey();
		
		// Get team id from key
		$teamID = self::getTeamID();
		
		// Validate given key with the team
		return parent::validate($akey, $teamID);
	}
	
	/**
	 * Get the team id from the given api key.
	 * 
	 * @return	integer
	 * 		The team id or NULL on error.
	 */
	public static function getTeamID()
	{
		// Get current key
		$akey = self::getAPIKey();
		
		// Get team id from key
		return parent::getTeamID($akey);
	}
	
	/**
	 * Get the current request api key.
	 * 
	 * @return	string
	 * 		The API key.
	 */
	private static function getAPIKey()
	{
		return engine::getVar("akey");
	}
}
//#section_end#
?>