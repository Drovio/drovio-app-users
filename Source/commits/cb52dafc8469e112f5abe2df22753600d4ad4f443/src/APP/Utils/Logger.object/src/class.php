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
use \APP\Identity\account;

/**
 * Activity logger
 * 
 * This is an abstract activity logger
 * 
 * @version	0.1-1
 * @created	November 18, 2015, 17:00 (GMT)
 * @updated	November 18, 2015, 17:00 (GMT)
 */
class Logger
{
	/**
	 * Create a new log entry.
	 * 
	 * @param	string	$type
	 * 		The log type.
	 * 
	 * @param	string	$message
	 * 		The log description.
	 * 
	 * @param	boolean	$includeTimestamp
	 * 		Whether to include a timestamp in the log or not.
	 * 
	 * @param	string	$path
	 * 		The log file path folder.
	 * 
	 * @param	string	$filename
	 * 		The log file name inside the folder.
	 * 
	 * @return	boolean
	 * 		True on success, false on failure.
	 */
	public static function log($type, $message, $includeTimestamp, $path, $filename)
	{
		// Initialize xml parser in team mode and get root
		$xmlParser = self::initialiseParser($path, $filename);
		$root = $xmlParser->evaluate("/root_element")->item(0);
		
		// Create new log
		$logEntry = $xmlParser->create($type, $message);
		$xmlParser->append($root, $logEntry);
		
		// Append timestamp if requested
		if ($includeTimestamp)
			$xmlParser->attr($logEntry, "timestamp", time());
		
		// Update log
		return $xmlParser->update();
	}
	
	/**
	 * Initialize the logger by creating the log file.
	 * 
	 * @param	string	$path
	 * 		The log file path folder.
	 * 
	 * @param	string	$filename
	 * 		The log file name inside the folder.
	 * 
	 * @return	DOMParser
	 * 		The DOMParser loaded with the xml file.
	 */
	private static function initialiseParser($path, $filename)
	{
		// Initialize DOMParser
		$xmlParser = new DOMParser($mode = DOMParser::TEAM_MODE, $shared = FALSE);
		
		// Make sure today's log doesn't already exist
		try
		{
			$xmlParser->load($path.$filename, $preserve = FALSE);
		}
		catch (Exception $ex)
		{
			// File does not exist, we create it
			$root = $xmlParser->create("root_element");
			$xmlParser->append($root);
			$xmlParser->save($path = $path, $fileName = $filename, $format = FALSE);
		}
		
		// Return the DOMParser object
		return $xmlParser;
	}
}
//#section_end#
?>