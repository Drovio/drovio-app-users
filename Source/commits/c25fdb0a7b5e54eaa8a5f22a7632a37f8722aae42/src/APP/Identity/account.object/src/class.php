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
 * @version	5.0-1
 * @created	October 11, 2015, 18:28 (BST)
 * @updated	November 13, 2015, 15:13 (GMT)
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
	 * Gets the current logged in account id.
	 * 
	 * @return	integer
	 * 		The account id.
	 */
	public function getAccountID()
	{
		// Get account from parent
		$accountID = parent::getAccountID();
		if (!empty($accountID))
			return $accountID;

		// Get value from engine
		$accountID = engine::getVar("acc");
		return $this->accountID = $accountID;
	}
	
	/**
	 * Gets the current mx id.
	 * 
	 * @return	string
	 * 		The current mx id.
	 */
	public function getMX()
	{
		// Get mx from parent
		$mx = parent::getMX();
		if (!empty($mx))
			return $mx;

		// Get value from engine
		$mx = engine::getVar("mx");
		return $this->mxID = $mx;
	}
	
	/**
	 * Gets the person id of the logged in account.
	 * 
	 * @return	integer
	 * 		The person id.
	 */
	public function getPersonID()
	{
		// Get person id
		$personID = parent::getPersonID();
		if (!empty($personID))
			return $personID;
		
		// Get value from engine
		$personID = engine::getVar("person");
		return $this->personID = $personID;
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