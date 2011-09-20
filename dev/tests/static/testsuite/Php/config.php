<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  static_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$baseDir = realpath(__DIR__ . '/../../../../../');

return array(
    'report_dir' => "{$baseDir}/dev/tests/static/report",
    'white_list' => array(
        "{$baseDir}/dev/tests/integration/framework/tests/unit/testsuite/Magento/Test/TestSuite",
        "{$baseDir}/dev/tests/integration/framework/tests/unit/testsuite/Magento/Test/Helper",
        "{$baseDir}/dev/tests/integration/framework/Magento/Test/Helper",
        "{$baseDir}/dev/tests/integration/framework/Magento/Test/Profiler",
        "{$baseDir}/dev/tests/integration/framework/Magento/Test/TestSuite",
        "{$baseDir}/dev/tests/integration/framework/Magento/Test/TestCase",
        "{$baseDir}/dev/tests/integration/testsuite/AllRelevantTests.php",
        "{$baseDir}/dev/tests/static",
        "{$baseDir}/dev/tools/batch_tests/source"
    ),
    'black_list' => array(
        /* Files that intentionally violate the requirements for testing purposes */
        "{$baseDir}/dev/tests/static/testsuite/Php/Exemplar/_files/phpcs/input",
        "{$baseDir}/dev/tests/static/testsuite/Php/Exemplar/_files/phpmd/input",
        "{$baseDir}/dev/tests/integration/framework/tests/unit/testsuite/Magento/Test/TestSuite/_files",
    )
);
