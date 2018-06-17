<?php
//#section#[header]
// Namespace
namespace APP\Resources;

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
importer::import("AEL", "Resources", "DOMParser");

use \AEL\Resources\DOMParser;

class activityLog
{
	private $xmlParser;
	
	private static $instance;
	
	public static function getInstance()
	{
		// Check instance
		if (!isset(self::$instance))
			self::$instance = new activityLog();
		
		// Return instance
		return self::$instance;
	}
	
	protected function __construct()
	{
		// Initialize parser
		$this->xmlParser = new DOMParser($mode = DOMParser::TEAM_MODE, $shared = FALSE);
		
		// Check today's file
		$this->createDayFile();
	}
	
	private function createDayFile()
	{
		// Try to load today's file
		$fileName = "/activity/log_".date("Y-m-d", time()).".xml";
		try
		{
			$this->xmlParser->load($fileName);
		}
		catch (Exception $ex)
		{
			$root = $this->xmlParser->create("log");
			$this->xmlParser->append($root);
			$this->xmlParser->save($fileName);
		}
	}
}
//#section_end#
?>