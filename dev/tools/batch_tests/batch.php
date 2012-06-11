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
    '../../tests/unit/framework/tests/unit' => '',
    '../../tests/unit' => '',
    '../../tests/static/framework/tests/unit' => '',
    '../../tests/integration/framework/tests/unit' => '',
    '../../tests/integration' => '',
    '../../tests/static' => '',
);
$arguments = getopt('', array('all'));
if (isset($arguments['all'])) {
    $tests['../../tests/static'] = ' -c phpunit-all.xml.dist';
}

$failures = array();
foreach ($tests as $dir => $options) {
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
