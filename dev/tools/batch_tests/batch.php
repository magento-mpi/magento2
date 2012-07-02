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
    0 => array('../../tests/unit/framework/tests/unit', ''),
    1 => array('../../tests/unit', ''),
    2 => array('../../tests/static/framework/tests/unit', ''),
    3 => array('../../tests/integration/framework/tests/unit', ''),
    4 => array('../../tests/integration', ''),
    5 => array('../../tests/static', ''),
);
$arguments = getopt('', array('all'));
if (isset($arguments['all'])) {
    $tests[] = array('../../tests/integration', ' testsuite/integrity');
    $tests[5][1] = ' -c phpunit-all.xml.dist';
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
