<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Light bootstrap script - does not initialize Magento instance.
 * Used for compatibility testsuites.
 */


$baseDir = dirname(__DIR__);

/*
 * Setup include path for autoload purpose.
 * Include path setup is intentionally moved out from the phpunit.xml to simplify maintenance of CI builds.
 */
set_include_path(implode(
    PATH_SEPARATOR,
    array(
        "$baseDir/framework",
        "$baseDir/testsuite",
        get_include_path()
    )
));

if (!defined('TEST_FIXTURE_DIR')) {
    define('TEST_FIXTURE_DIR', "$baseDir/fixture");
}

$magentoBootstrap = realpath("$baseDir/../../../app/bootstrap.php");

require_once $magentoBootstrap;

/* Enable profiler if necessary */
if (defined('TESTS_PROFILER_FILE') && TESTS_PROFILER_FILE) {
    Magento_Profiler::registerOutput(
        new Magento_Profiler_Output_Csvfile($baseDir . DIRECTORY_SEPARATOR . TESTS_PROFILER_FILE)
    );
}

/* Enable profiler with bamboo friendly output format */
if (defined('TESTS_BAMBOO_PROFILER_FILE') && defined('TESTS_BAMBOO_PROFILER_METRICS_FILE')) {
    Magento_Profiler::registerOutput(new Magento_Test_Profiler_OutputBamboo(
        $baseDir . DIRECTORY_SEPARATOR . TESTS_BAMBOO_PROFILER_FILE,
        require($baseDir . DIRECTORY_SEPARATOR . TESTS_BAMBOO_PROFILER_METRICS_FILE)
    ));
}

/* Activate custom annotations in doc comments */
/*
 * Note: order of registering (and applying) annotations is important.
 * To allow config fixtures to deal with fixture stores, data fixtures should be processed before config fixtures.
 */
Magento_Test_Listener::registerObserver('Magento_Test_Listener_Annotation_Fixture');

/* Unset declared global variables to release PHPUnit from maintaining their values between tests */
unset($baseDir);
