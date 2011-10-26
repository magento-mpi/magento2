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
    '../../tests/unit' => '',
    '../../tests/static/framework/tests/unit' => '',
    '../../tests/static' => 'testsuite/Php/LiveCodeTest.php',
    '../../tests/integration/framework/tests/unit' => '',
    '../../tests/integration' => ''
);

$failures = array();
foreach ($tests as $dir => $options) {
    $dirName = __DIR__ . '/' . $dir;
    chdir($dirName);
    echo "\n\n";
    echo str_pad("----" . realpath($dirName), 70, '-');
    echo "\n\n";
    passthru('phpunit ' . $options, $returnVal);
    if ($returnVal) {
        $failures[] = $dirName;
    }
}

echo "\n" , str_repeat('-', 70), "\n";
if ($failures) {
    echo "\nFAILED - " . count($failures) . ' of ' . count($tests) . ":\n";
    foreach ($failures as $dir) {
        echo ' - ' . realpath($dir) . "\n";
    }
} else {
    echo "\nPASSED (" . count($tests) . ")\n";
}
