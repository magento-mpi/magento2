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

$commands = array(
    'unit'                  => array('../../tests/unit', ''),
    'unit-performance'      => array('../../tests/performance/framework/tests/unit', ''),
    'unit-static'           => array('../../tests/static/framework/tests/unit', ''),
    'unit-integration'      => array('../../tests/integration/framework/tests/unit', ''),
    'integration'           => array('../../tests/integration', ''),
    'static'                => array('../../tests/static', ' -c phpunit-all.xml.dist'),
    'integration-integrity' => array('../../tests/integration', ' testsuite/integrity'),
    'legacy'                => array('../../tests/static', ' testsuite/Legacy'),
);
$types = array(
    'unit'        => array('unit', 'unit-performance', 'unit-static', 'unit-integration'),
    'integration' => array('integration', 'integration-integrity'),
    'static'      => array('static'),
    'integrity'   => array('static', 'integration-integrity'),
    'legacy'      => array('legacy'),
);
$arguments = getopt('', array('all', 'type::'));
if (isset($arguments['type'])) {
    $availableTypes = array_keys($commands);
    foreach ($availableTypes as $type) {
        if (!in_array($type, $types[$arguments['type']])) {
            unset($commands[$type]);
        }
    }
}
if (isset($arguments['all'])) {
    unset($commands['legacy']); // the static with "all" option covers it
} else {
    unset($commands['integration-integrity'], $commands['legacy']);
    if (isset($commands['static'])) {
        $commands['static'][1] = '';
    }
}

$failures = array();
foreach ($commands as $row) {
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
    echo "\nFAILED - " . count($failures) . ' of ' . count($commands) . ":\n";
    foreach ($failures as $message) {
        echo ' - ' . $message . "\n";
    }
} else {
    echo "\nPASSED (" . count($commands) . ")\n";
}
