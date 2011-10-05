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
        "{$baseDir}/app/code/core/Mage/Core/Model/Design.php",
        "{$baseDir}/dev/tests/integration",
        "{$baseDir}/dev/tests/static",
        "{$baseDir}/lib/Magento/Profiler",
        "{$baseDir}/lib/Magento/Profiler.php",
        "{$baseDir}/lib/Varien/Object.php",
        "{$baseDir}/app/code/core/Mage/Index/Model/Indexer/Abstract.php",
    ),
    'black_list' => array(
        /* Files that intentionally violate the requirements for testing purposes */
        "{$baseDir}/dev/tests/static/testsuite/Php/Exemplar/_files/phpcs/input",
        "{$baseDir}/dev/tests/static/testsuite/Php/Exemplar/_files/phpmd/input",
        "{$baseDir}/dev/tests/integration/framework/tests/unit/testsuite/Magento/Test/TestSuite/_files",
    )
);
