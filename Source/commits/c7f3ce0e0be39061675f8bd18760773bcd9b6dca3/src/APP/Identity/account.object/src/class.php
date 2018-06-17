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

importer::import("API", "Profile", "team");
importer::import("AEL", "Identity", "account");

use \API\Profile\team;
use \AEL\Identity\account as IDAccount;

/**
 * Account management class
 * 
 * This class handles the account connection to the team identity database.
 * 
 * @version	3.0-1
 * @created	October 11, 2015, 18:28 (BST)
 * @updated	October 23, 2015, 20:06 (BST)
 */
class account extends IDAccount
{
	/**
	 * Initialize the identity engine for the team making the api request.
	 * It will get the team info from the api key given.
	 * 
	 * @return	account
	 * 		The account instance.
	 */
	public static function getAPIInstance()
	{
		return parent::getInstance();
	}
	
	/**
	 * Initialize the identity engine for the current team.
	 * 
	 * @return	void
	 */
	public static function init()
	{
		// Get team name
		$teamName = strtolower(team::getTeamUName());
		
		// Initialize identity
		parent::init($teamName);
	}
}
//#section_end#
?>