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
application::import('Identity', 'account');

use \AEL\Resources\DOMParser;
use \APP\Utils\Logger;

/**
 * Day Activity Logger
 * 
 * Logs daily activity to a file.
 * 
 * @version	0.1-1
 * @created	November 18, 2015, 17:32 (GMT)
 * @updated	November 18, 2015, 17:32 (GMT)
 */
class DayLogger extends Logger
{
	/**
	 * Log a daily activity.
	 * 
	 * @param	string	$logType
	 * 		The log type.
	 * 
	 * @param	string	$message
	 * 		The log message, if any.
	 * 
	 * @return	boolean
	 * 		True on success, false on failure.
	 */
	public static function log($logType, $message)
	{
		$todayFile = self::getDayFile();
		return parent::log($logType, $message, true, "/logs/", $todayFile);
	}
	
	/**
	 * Get all sign ups for the current day.
	 * 
	 * @return	integer
	 * 		Total sign ups for the current day.
	 */
	public static function getTodaySignupCount()
	{
		$todayFile = self::getDayFile();
		return DayLogger::registrationCounts($todayFile);
	}
	
	/**
	 * Get sign up count for the last n days.
	 * 
	 * @param	integer	$n
	 * 		The number of days to look back for signups.
	 * 
	 * @return	integer
	 * 		Count of sign ups for the last n days.
	 */
	public static function getLastNDaysSignupCount($n)
	{
		$count = 0;
		$day = 60*60*24;
		for ($i = 0; $i < $n; $i++)
		{
			$dayFile = self::getDayFile(time() - $i * $day);
			$count += DayLogger::registrationCounts($dayFile);
		}
		return $count;
	}
	
	/**
	 * Get total signups for a given day via the filename of the activity file log.
	 * 
	 * @param	string	$filename
	 * 		The activity log filename.
	 * 
	 * @return	integer
	 * 		The total signups.
	 */
	private static function registrationCounts($filename)
	{
		$xmlParser = new DOMParser($mode = DOMParser::TEAM_MODE, $shared = FALSE);
		// make sure today's log doesn't already exist
		try
		{
			// If file already exists 
			$xmlParser->load("/logs/".$filename, $preserve = FALSE);
		}
		catch (Exception $ex)
		{
			return 0;
		}
		
		$usersCreated = $xmlParser->evaluate("//descendant::created-user");
		$usersSignedup = $xmlParser->evaluate("//descendant::signup");
		return $usersCreated->length + $usersSignedup->length;
	}
	
	/**
	 * Get the filename of a log file for a given day.
	 * 
	 * @param	integer	$time
	 * 		The timestamp to represent the day.
	 * 		If empty get the current time.
	 * 		It is empty by default.
	 * 
	 * @return	string
	 * 		The filename.
	 */
	private static function getDayFile($time = NULL)
	{
		$time = (empty($time) ? time() : $time);
		return "day".date('Y-m-d', $time).".xml";;
	}
}
//#section_end#
?>