<?php
//#section#[header]
// Use Important Headers
use \API\Platform\importer;
use \API\Platform\engine;
use \Exception;

// Check Platform Existance
if (!defined('_RB_PLATFORM_')) throw new Exception("Platform is not defined!");

// Import DOM, HTML
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

use \UI\Html\DOM;
use \UI\Html\HTML;

// Import application for initialization
importer::import("AEL", "Platform", "application");
use \AEL\Platform\application;

// Increase application's view loading depth
application::incLoadingDepth();

// Set Application ID
$appID = 89;

// Init Application and Application literal
application::init(89);
// Secure Importer
importer::secure(TRUE);

// Import SDK Packages
importer::import("AEL", "Resources");
importer::import("UI", "Apps");

// Import APP Packages
application::import("Identity");
//#section_end#
//#section#[view]
use \AEL\Resources\DOMParser;
use \APP\Identity\account;

class Logging {
	private static function initialiseParser($path, $filename) {
		$xmlParser = new DOMParser($mode = DOMParser::TEAM_MODE, $shared = FALSE);
		// make sure today's log doesn't already exist
		try {
			// If file already exists 
			$xmlParser->load($path = $path.$filename, $preserve = FALSE);
		} catch (Exception $ex) {
			// File does not exist, we create it
			$root = $xmlParser->create("root_element");
			$xmlParser->append($root);
			$xmlParser->save($path = $path, $fileName = $filename, $format = FALSE);
			$xmlParser->load($path = $path.$filename, $preserve = FALSE);
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
			echo date("d/m/Y G:i:s");
			$timestampElem = $xmlParser->create("timestamp", date("D/m/Y G:i:s"));
			$xmlParser->append($newLog, $timestampElem);
		}
		$xmlParser->append($root, $newLog);
		$xmlParser->update();
	}
}

class DayLogging extends Logging{
	public static function log($actionType, $message) {
		$todayFile = "day".date('Y-m-d').".xml";
		parent::log($actionType, $message, true, "/logs/", $todayFile);
	}
}

class UserLogging extends Logging{

	public static function log($userId, $actionType, $message) {
		$filename = $userId."_userlog.xml";
		parent::log($actionType, $message, true, "/logs/userlogs/", $filename);
	}
}
//#section_end#
?>