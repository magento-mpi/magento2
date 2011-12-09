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
    array('../../tests/unit', ''),
    array('../../tests/static/framework/tests/unit', ''),
    array('../../tests/static', ''),
    array('../../tests/integration/framework/tests/unit', ''),
    array('../../tests/integration', '')
);
$arguments = getopt('', array('legacy::', 'all::'));
if (isset($arguments['legacy']) || isset($arguments['all'])) {
    $tests[] = array('../../tests/static', 'testsuite/Legacy');
}
if (isset($arguments['all'])) {
    $tests[] = array('../../tests/static', 'testsuite/Php/CodeMessTest.php');
    $tests[] = array('../../tests/static', 'testsuite/Php/Exemplar');
}

$failures = array();
foreach ($tests as $line) {
    list($dir, $options) = $line;
    $dirName = realpath(__DIR__ . '/' . $dir);
    chdir($dirName);
    $command = 'phpunit ' . $options;
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
