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
use \APP\Utils\Logger;

class DayLogger extends Logger {
	
	public static function log($actionType, $message) {
		$todayFile = "day".date('Y-m-d').".xml";
		parent::log($actionType, $message, true, "/logs/", $todayFile);
	}
}
//#section_end#
?>