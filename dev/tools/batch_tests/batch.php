<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     tools
 * @subpackage  batch_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$tests = array(
    'unit-unit'        => array('../../tests/unit/framework/tests/unit', ''),
    'unit'             => array('../../tests/unit', ''),
    'unit-performance' => array('../../tests/performance/framework/tests/unit', ''),
    'unit-static'      => array('../../tests/static/framework/tests/unit', ''),
    'unit-integration' => array('../../tests/integration/framework/tests/unit', ''),
    'integration'      => array('../../tests/integration', ''),
    'static'           => array('../../tests/static', ''),
);
$arguments = getopt('', array('all'));
if (isset($arguments['all'])) {
    $tests['integration-integrity'] = array('../../tests/integration', ' testsuite/integrity');
    $tests['static'][1] = ' -c phpunit-all.xml.dist';
}

$failures = array();
foreach ($tests as $row) {
    list($dir, $options) = $row;
    $dirName = realpath(__DIR__ . '/' . $dir);
    chdir($dirName);
    $command = 'phpunit' . $options;
    $message = $dirName . '> ' . $command;
    echo "\n\n";
    echo str_pad("---- {$message} ", 70, '-');
    echo "\n\n";
    passthru($command, $returnVal);
    if ($returnVal) {
        $failures[] = $message;
    }
}

echo "\n" , str_repeat('-', 70), "\n";
if ($failures) {
    echo "\nFAILED - " . count($failures) . ' of ' . count($tests) . ":\n";
    foreach ($failures as $message) {
        echo ' - ' . $message . "\n";
    }
} else {
    echo "\nPASSED (" . count($tests) . ")\n";
}
