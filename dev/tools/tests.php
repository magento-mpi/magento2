<?php
/**
 * Batch tool for running all or some of tests
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$vendorDir = require '../../app/etc/vendor_path.php';

$commands = array(
    'unit'                   => array('../tests/unit', ''),
    'unit-performance'       => array('../tests/performance/framework/tests/unit', ''),
    'unit-static'            => array('../tests/static/framework/tests/unit', ''),
    'unit-integration'       => array('../tests/integration/framework/tests/unit', ''),
    'integration'            => array('../tests/integration', ''),
    'integration-integrity'  => array('../tests/integration', ' testsuite/Magento/Test/Integrity'),
    'static-default'         => array('../tests/static', ''),
    'static-legacy'          => array('../tests/static', ' testsuite/Magento/Test/Legacy'),
    'static-integration-php' => array('../tests/static', ' testsuite/Magento/Test/Php/Exemplar'),
    'static-integration-js'  => array('../tests/static', ' testsuite/Magento/Test/Js/Exemplar'),
);
$types = array(
    'all'             => array_keys($commands),
    'unit'            => array('unit', 'unit-performance', 'unit-static', 'unit-integration'),
    'integration'     => array('integration'),
    'integration-all' => array('integration', 'integration-integrity'),
    'static'          => array('static-default'),
    'static-all'      => array('static-default', 'static-legacy', 'static-integration-php', 'static-integration-js'),
    'integrity'       => array('static-default', 'static-legacy', 'integration-integrity'),
    'legacy'          => array('static-legacy'),
    'default'         => array(
        'unit', 'unit-performance', 'unit-static', 'unit-integration', 'integration', 'static-default'
    ),
);

$arguments = getopt('', array('type::'));
if (!isset($arguments['type'])) {
    $arguments['type'] = 'default';
} elseif (!isset($types[$arguments['type']])) {
    echo "Invalid type: '{$arguments['type']}'. Available types: " . implode(', ', array_keys($types)) . "\n\n";
    exit(1);
}

$failures = array();
$runCommands = $types[$arguments['type']];
foreach ($runCommands as $key) {
    list($dir, $options) = $commands[$key];
    $dirName = realpath(__DIR__ . '/' . $dir);
    chdir($dirName);
    $command = realpath(__DIR__ . '/../../') . '/' . $vendorDir . '/phpunit/phpunit/phpunit' . $options;
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
    echo "\nFAILED - " . count($failures) . ' of ' . count($runCommands) . ":\n";
    foreach ($failures as $message) {
        echo ' - ' . $message . "\n";
    }
} else {
    echo "\nPASSED (" . count($runCommands) . ")\n";
}
