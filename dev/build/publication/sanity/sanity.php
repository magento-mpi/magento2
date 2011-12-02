#!/usr/bin/php
<?php
/**
 * {license_notice}
 *
 * @category   build
 * @package    sanity
 * @copyright  {copyright}
 * @license    {license_link}
 */
require dirname(__FILE__) . '/SanityRoutine.php';

define('USAGE', <<<USAGE
$>./sanity.php -c ce.xml
    additional parameters:
    -w dir   use specified working dir instead of current

USAGE
);

$shortOpts = 'c:w:v';
$options = getopt($shortOpts);

if (!isset($options['c'])) {
    print USAGE;
    exit(1);
}

$configFile = $options['c'];
if (!file_exists($configFile)) {
    print 'File "' . $configFile . '" does not exist (current dir is "' . getcwd() . '").' . "\n";
    exit(1);
}

$config = SanityRoutine::loadConfig($configFile);
if (!$config) {
    print "Problem with config file\n";
    exit(1);
}
if (!$config['words']) {
    print "No words to check\n";
    exit(1);
}

$workingDir = dirname(__FILE__);
if (isset($options['w'])) {
    $workingDir = $options['w'];
}
$workingDir = rtrim($workingDir, '/\\');
if (!is_dir($workingDir)) {
    print 'Working dir "' . $workingDir . '" does not exist' . "\n";
    exit(1);
}

$verbose = isset($options['v']) ? true : false;
SanityRoutine::$verbose = $verbose;

// ---Process--------------------------
SanityRoutine::printVerbose('Searching for ' . count($config['words']) . ' words');

$found = SanityRoutine::findWords($workingDir, $workingDir, $config);
if ($found) {
    foreach ($found as $info) {
        echo 'Found [' . implode(', ', $info['words']) . '] in ' . $info['file'] . "\n";
    }
    exit(1);
}

exit(0);
