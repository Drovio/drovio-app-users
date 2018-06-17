<?php
//#section#[header]
// Namespace
namespace APP\Utils;

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
 * @package	Utils
 * 
 * @copyright	Copyright (C) 2015 DrovioUserManagement. All rights reserved.
 */

importer::import('AEL', 'Resources', 'DOMParser');

use \APP\Utils\Logger;

/**
 * User logger
 * 
 * This is an advanced logger that logs activity for each user separately.
 * 
 * @version	0.1-1
 * @created	November 18, 2015, 17:04 (GMT)
 * @updated	November 18, 2015, 17:04 (GMT)
 */
class UserLogger extends Logger
{
	/**
	 * Log a user activity.
	 * 
	 * @param	integer	$userID
	 * 		The user id to log for.
	 * 
	 * @param	string	$logType
	 * 		The log type.
	 * 
	 * @param	string	$message
	 * 		The message of the log, if any.
	 * 
	 * @return	boolean
	 * 		True on success, false on failure.
	 */
	public static function log($userID, $logType, $message)
	{
		$filename = $userID."_userlog.xml";
		return TRUE;//parent::log($logType, $message, true, "/logs/userlogs/", $filename);
	}
}
//#section_end#
?>