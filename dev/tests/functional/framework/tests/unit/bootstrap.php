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

define('SELENIUM_TESTS_LIBDIR', realpath(__DIR__ . '/../..'));
define('SELENIUM_TESTS_BASEDIR', realpath(SELENIUM_TESTS_LIBDIR . DIRECTORY_SEPARATOR . '..'));
define('SELENIUM_TESTS_SCREENSHOTDIR',
        realpath(SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'screenshots'));
define('SELENIUM_TESTS_LOGS',
        realpath(SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'logs'));
$autoload = require __DIR__ . '/../../../../autoload.php';
$autoload->addIncludePath(array(__DIR__, SELENIUM_TESTS_LIBDIR));
