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
importer::import('AEL', 'Resources', 'DOMParser');
application::import('Identity', 'account');

use \AEL\Resources\DOMParser;
use \APP\Identity\account;

class Logger {
	private static function initialiseParser($path, $filename) {
		$xmlParser = new DOMParser($mode = DOMParser::TEAM_MODE, $shared = FALSE);
		// make sure today's log doesn't already exist
		try {
			// If file already exists 
			$xmlParser->load($path.$filename, $preserve = FALSE);
		} catch (Exception $ex) {
			// File does not exist, we create it
			$root = $xmlParser->create("root_element");
			$xmlParser->append($root);
			$xmlParser->save($path = $path, $fileName = $filename, $format = FALSE);
		}
		return $xmlParser;
	}
	
	public static function log($tagName, $message, $includeTimestamp, $path, $filename) {
		// Initialize xml parser in team mode
		$xmlParser = self::initialiseParser($path, $filename);
		$newLog = $xmlParser->create($tagName, $message);
		$root = $xmlParser->evaluate("/root_element")->item(0);
		
		// Append timestamp if requested
		if ($includeTimestamp) {
			$timestampElem = $xmlParser->create("timestamp", time());
			$xmlParser->append($newLog, $timestampElem);
		}
		$xmlParser->append($root, $newLog);
		$xmlParser->update();
	}
}
//#section_end#
?>