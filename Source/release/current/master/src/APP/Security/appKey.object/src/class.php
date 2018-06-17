<?php
//#section#[header]
// Namespace
namespace APP\Security;

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
 * @package	Security
 * 
 * @copyright	Copyright (C) 2015 DrovioUserManagement. All rights reserved.
 */

application::import("Security", "publicAppKey");

use \APP\Security\publicAppKey;

/**
 * Application key validator.
 * 
 * Extends the Application public key interface and can validate the received key from the API request.
 * 
 * @version	2.0-1
 * @created	October 18, 2015, 12:55 (BST)
 * @updated	October 31, 2015, 17:09 (GMT)
 * 
 * @deprecated	Use \APP\Security\publicAppKey instead.
 */
class appKey extends publicAppKey {}
//#section_end#
?>