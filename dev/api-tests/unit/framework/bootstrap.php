<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   {copyright}
 * @license     {license_link}
 */

$_rootDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..'
    . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..';
require_once $_rootDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'bootstrap.php';
set_include_path(
    get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . PATH_SEPARATOR
        . dirname(__FILE__). DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'testsuite'
);
chdir($_rootDir);

//need to initialize test App configuration in bootstrap
//because data providers in test cases are run before setUp() and even before setUpBeforeClass() methods in TestCase.
Mage_PHPUnit_Initializer_Factory::createInitializer('Mage_PHPUnit_Initializer_App')->run();
