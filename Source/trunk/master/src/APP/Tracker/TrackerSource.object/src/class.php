<?php
//#section#[header]
// Namespace
namespace APP\Tracker;

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
importer::import("UBA", "Analytics", "tracker");

use \UBA\Analytics\tracker;

class TrackerSource
{
	// Constructor Method
	public function __construct()
	{
		// Put your constructor method code here.
	}
	
	public static function getInstance() {
		return tracker::getInstance("drovio");
	}
}
//#section_end#
?>