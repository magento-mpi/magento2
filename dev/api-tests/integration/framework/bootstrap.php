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

require dirname(__FILE__) . '/Magento/Test/Bootstrap.php';

$baseDir = dirname(dirname(__FILE__));

/*
 * Setup include path for autoload purpose.
 * Include path setup is intentionally moved out from the phpunit.xml to simplify maintenance of CI builds.
 */
set_include_path(implode(
    PATH_SEPARATOR,
    array(
        "$baseDir/framework",
        get_include_path()
    )
));

$testEtcDir = "$baseDir/framework";
if (defined('TESTS_ETC_DIRECTORY') && TESTS_ETC_DIRECTORY && is_dir(TESTS_ETC_DIRECTORY)) {
    $testEtcDir = array($testEtcDir, realpath(TESTS_ETC_DIRECTORY));
}

Magento_Test_Bootstrap::setInstance(new Magento_Test_Bootstrap(
    (defined('TESTS_DB_VENDOR') ? TESTS_DB_VENDOR : 'mysql'),
    realpath("$baseDir/../../../"),
    $testEtcDir,
    "$baseDir/tmp"
));
if (defined('TESTS_SHUTDOWN_METHOD') && TESTS_SHUTDOWN_METHOD) {
    Magento_Test_Bootstrap::getInstance()->setShutdownAction(TESTS_SHUTDOWN_METHOD);
}

/* Enable profiler if necessary */
if (defined('TESTS_PROFILER_FILE') && TESTS_PROFILER_FILE) {
    Magento_Test_Profiler::registerOutput(
        new Magento_Test_Profiler_Output_Csvfile($baseDir . TESTS_PROFILER_FILE)
    );
}

/* Enable profiler with bamboo friendly output format */
if (defined('TESTS_BAMBOO_PROFILER_FILE') && defined('TESTS_BAMBOO_PROFILER_METRICS_FILE')) {
    Magento_Test_Profiler::registerOutput(new Magento_Test_Profiler_Output_Bamboo(
        $baseDir . TESTS_BAMBOO_PROFILER_FILE,
        require($baseDir . TESTS_BAMBOO_PROFILER_METRICS_FILE)
    ));
}

/**
 * Define enable show content on get invalid response
 */
!defined('TESTS_WEBSERVICE_SHOW_INVALID_RESPONSE') &&
    define('TESTS_WEBSERVICE_SHOW_INVALID_RESPONSE', '1');

/* Activate custom annotations in doc comments */
/*
 * Note: order of registering (and applying) annotations is important.
 * To allow config fixtures to deal with fixture stores, data fixtures should be processed before config fixtures.
 */
Magento_Test_Listener::registerObserver('Magento_Test_Listener_Annotation_Isolation');
Magento_Test_Listener::registerObserver('Magento_Test_Listener_Annotation_Fixture');
Magento_Test_Listener::registerObserver('Magento_Test_Listener_Annotation_Config');

/* Unset declared global variables to release PHPUnit from maintaining their values between tests */
unset($baseDir);
