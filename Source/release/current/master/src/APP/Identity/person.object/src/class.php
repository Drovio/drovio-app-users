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
importer::import("AEL", "Identity", "person");
application::import("Security", "appKey");

use \API\Profile\team;
use \AEL\Identity\person as IDPerson;
use \APP\security\appKey;

/**
 * Person management class
 * 
 * This class handles the person connection to the team identity database.
 * 
 * @version	0.1-1
 * @created	October 23, 2015, 20:06 (BST)
 * @updated	October 23, 2015, 20:06 (BST)
 */
class person extends IDPerson
{	
	/**
	 * Initialize the identity engine for the team making the api request.
	 * It will get the team info from the api key given.
	 * 
	 * @return	void
	 */
	public static function getAPIInstance()
	{
		return parent::getInstance();
	}
}
//#section_end#
?>