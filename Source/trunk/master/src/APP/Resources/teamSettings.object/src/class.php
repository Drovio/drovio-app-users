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
/**
 * @library	APP
 * @package	Resources
 * 
 * @copyright	Copyright (C) 2016 loginBox. All rights reserved.
 */

importer::import("AEL", "Resources", "appSettings");

use \AEL\Resources\appSettings;

/**
 * Generic settings for every managed team.
 * 
 * It stores settings including authentication options, payment types and more.
 * 
 * @version	0.1-2
 * @created	January 6, 2016, 17:22 (GMT)
 * @updated	January 6, 2016, 17:30 (GMT)
 */
class teamSettings extends appSettings
{
	/**
	 * Create a new team settings instance.
	 * 
	 * @param	string	$teamName
	 * 		The team name to manage settings for.
	 * 
	 * @return	void
	 */
	public function __construct($teamName)
	{
		// Normalize team name for the path
		$uname = strtolower($teamName);
		$uname = trim($uname, "._ ");
		$uname = str_replace(" ", "_", $uname);
		$uname = str_replace(".", "_", $uname);
		
		// Initialize settings
		$settingsFolder = "/Settings/".$uname;
		parent::__construct($mode = appSettings::TEAM_MODE, $shared = FALSE, $settingsFolder, $filename = "settings");
	}
}
//#section_end#
?>