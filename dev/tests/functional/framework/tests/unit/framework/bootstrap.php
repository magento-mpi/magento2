<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

define('SELENIUM_TESTS_BASEDIR', realpath(__DIR__ . DIRECTORY_SEPARATOR . '..'
    . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'));
define('SELENIUM_TESTS_FWDIR', realpath(SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'framework'));
require __DIR__ . '/../../../../../../../app/autoload.php';
Magento_Autoload_IncludePath::addIncludePath(array(__DIR__, SELENIUM_TESTS_FWDIR));
