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
use \APP\Utils\Logger;
use \APP\Utils\DayLogger;

class DayLogger extends Logger {
	
	public static function log($actionType, $message) {
		$todayFile = "day".date('Y-m-d').".xml";
		parent::log($actionType, $message, true, "/logs/", $todayFile);
	}
	
	private static function registrationCounts($filename) {
		$xmlParser = new DOMParser($mode = DOMParser::TEAM_MODE, $shared = FALSE);
		// make sure today's log doesn't already exist
		try {
			// If file already exists 
			$xmlParser->load("/logs/".$filename, $preserve = FALSE);
		} catch (Exception $ex) {
			return 0;
		}
		$users = $xmlParser->evaluate("//descendant::created-user");
		$userCount1 = $users->length;
		$users2 = $xmlParser->evaluate("//descendant::signup");
		return $userCount1 + $users2->length;
	}
	
	public static function getTodaySignupCount() {
		$todayFile = "day".date('Y-m-d').".xml";
		return DayLogger::registrationCounts($todayFile);
	}
	
	public static function getLastNDaysSignupCount($n) {
		$count = 0;
		$day = 60*60*24;
		for ($i = 0; $i < $n; $i++) {
			$dayFile = "day".date('Y-m-d', time() - $i * $day).".xml";
			$count += DayLogger::registrationCounts($dayFile);
		}
		return $count;
	}
}
//#section_end#
?>