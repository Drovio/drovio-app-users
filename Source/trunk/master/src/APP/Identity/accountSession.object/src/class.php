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
importer::import("AEL", "Identity", "accountSession");
application::import("Security", "appKey");

use \API\Profile\team;
use \AEL\Identity\accountSession as IDAccountSession;
use \APP\security\appKey;

/**
 * Account session management class
 * 
 * This class handles the account session connection to the team identity database.
 * 
 * @version	1.0-1
 * @created	October 21, 2015, 15:34 (BST)
 * @updated	October 23, 2015, 20:05 (BST)
 */
class accountSession extends IDAccountSession
{
	/**
	 * Initialize the identity engine for the team making the api request.
	 * It will get the team info from the api key given.
	 * 
	 * @return	accountSession
	 * 		The accountSession object.
	 */
	public static function getAPIInstance()
	{
		return parent::getInstance();
	}
}
//#section_end#
?>