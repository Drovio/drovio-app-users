<?php
//#section#[header]
// Namespace
namespace APP\Identity;

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
 * @package	Identity
 * 
 * @copyright	Copyright (C) 2015 DrovioUserManagement. All rights reserved.
 */

importer::import("API", "Platform", "engine");
importer::import("API", "Profile", "team");
importer::import("AEL", "Identity", "account");

use \API\Platform\engine;
use \API\Profile\team;
use \AEL\Identity\account as IDAccount;

/**
 * Account management class
 * 
 * This class handles the account connection to the team identity database.
 * 
 * @version	6.0-1
 * @created	October 11, 2015, 18:28 (BST)
 * @updated	November 14, 2015, 21:34 (GMT)
 */
class account extends IDAccount
{
	/**
	 * The platform account instance.
	 * 
	 * @type	account
	 */
	private static $instance;
	
	/**
	 * Initialize the identity engine for the current team.
	 * 
	 * @return	account
	 * 		The account instance.
	 */
	public static function getInstance()
	{
		// Check for an existing instance
		if (!isset(self::$instance))
			self::$instance = new account();
		
		// Return instance
		return self::$instance;
	}
	
	/**
	 * Get the authentication token from the request.
	 * 
	 * @return	string
	 * 		The authentication token.
	 */
	public function getAuthToken()
	{
		// Get token from parent
		$authToken = parent::getAuthToken();
		if (!empty($authToken))
			return $authToken;

		// Get value from engine
		$authToken = engine::getVar("id_auth_token");
		return $this->authToken = $authToken;
	}
	
	/**
	 * Initialize the identity engine for the team making the api request.
	 * It will get the team info from the api key given.
	 * 
	 * @return	account
	 * 		The account instance.
	 */
	public static function getAPIInstance()
	{
		return self::getInstance();
	}
	
	/**
	 * Initialize the current instance with account values.
	 * 
	 * @param	integer	$accountID
	 * 		The account id.
	 * 
	 * @param	string	$mxID
	 * 		The mx id.
	 * 
	 * @param	string	$personID
	 * 		The person id (if any).
	 * 		It is NULL by default.
	 * 
	 * @return	void
	 * 
	 * @deprecated	No longer needed since we use engine to get standard variable names.
	 */
	public function initialize($accountID, $mxID, $personID = NULL)
	{
		$this->accountID = $accountID;
		$this->mxID = $mxID;
		$this->personID = $personID;
	}
}
//#section_end#
?>