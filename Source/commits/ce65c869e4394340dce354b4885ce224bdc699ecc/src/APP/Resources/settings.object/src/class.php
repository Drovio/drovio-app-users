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
 * @copyright	Copyright (C) 2015 DrovioUserManagement. All rights reserved.
 */

importer::import("AEL", "Resources", "appSettings");

use \AEL\Resources\appSettings;

/**
 * Application settings for the current application.
 * 
 * {description}
 * 
 * @version	0.1-1
 * @created	October 15, 2015, 20:52 (EEST)
 * @updated	October 15, 2015, 20:52 (EEST)
 */
class settings extends appSettings
{
	/**
	 * Create a new application settings instance.
	 * 
	 * @return	void
	 */
	public function __construct()
	{
		parent::__construct($mode = appSettings::TEAM_MODE, $shared = FALSE, $settingsFolder = "/Settings/", $filename = "settings");
	}
}
//#section_end#
?>