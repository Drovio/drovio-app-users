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
importer::import("DRVC", "Profile", "account");

use \API\Profile\team;
use \DRVC\Profile\account as IDAccount;

/**
 * Account management class
 * 
 * This class handles the account connection to the team identity database.
 * 
 * @version	0.1-2
 * @created	October 11, 2015, 20:28 (EEST)
 * @updated	October 11, 2015, 20:29 (EEST)
 */
class account extends IDAccount
{
	/**
	 * Initialize the identity engine for the current team.
	 * 
	 * @return	void
	 */
	public static function init()
	{
		$teamName = team::getTeamUName();
		$teamName = strtolower($teamName);
		parent::init($teamName);
	}
}
//#section_end#
?>