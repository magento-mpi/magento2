<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/* Initialize DEV constants */
require_once dirname(dirname(dirname(__FILE__))) . '/config.php';
date_default_timezone_set('America/Los_Angeles');

define('UNIT_ROOT', DEV_ROOT . '/dev/api-tests/unit');
define('UNIT_FRAMEWORK', UNIT_ROOT . '/framework');
define('UNIT_TEMP', UNIT_ROOT . '/tmp');

if (file_exists(UNIT_FRAMEWORK . '/config.php')) {
    require_once 'config.php';
} else {
    require_once 'config.php.dist';
}

require_once UNIT_FRAMEWORK . '/autoloader.php';
require_once DEV_APP . '/Mage.php';

chdir(DEV_ROOT);


//need to initialize test App configuration in bootstrap
//because data providers in test cases are run before setUp() and even before setUpBeforeClass() methods in TestCase.
Mage_PHPUnit_Initializer_Factory::createInitializer('Mage_PHPUnit_Initializer_App')->run();
