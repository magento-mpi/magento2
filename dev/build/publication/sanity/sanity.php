#!/usr/bin/php
<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
$vendorDir = require __DIR__ . '/../../../../app/etc/vendor_path.php';
$vendorAutoload = __DIR__ . "/../../../../{$vendorDir}/autoload.php";
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
}

require_once __DIR__ . '/Magento/Tools/Sanity/SanityWordsFinder.php';

define(
    'USAGE',
<<<USAGE
php -f sanity.php -c <config_file> [-w <dir>] [-v]
    -c <config_file> path to configuration file with rules and white list
    [-w <dir>]       use specified working dir instead of current
    [-v]             verbose mode
USAGE
);

$shortOpts = 'c:w:v';
$options = getopt($shortOpts);

if (!isset($options['c'])) {
    echo USAGE;
    exit(1);
}
$configFile = $options['c'];

$workingDir = __DIR__;
if (isset($options['w'])) {
    $workingDir = $options['w'];
}

if (!file_exists($workingDir . '/CHANGELOG.md')) {
    echo "CHANGELOG.md file not found\n";
    exit(1);
}

echo "CHANGELOG.md file found\n";

$wordsFinder = new \Magento\Tools\Sanity\SanityWordsFinder($configFile, $workingDir);

$verbose = isset($options['v']) ? true : false;
if ($verbose) {
    $words = $wordsFinder->getSearchedWords();
    printf('Searching for banned words: "%s"...', implode('", "', $words));
}

$found = $wordsFinder->findWordsRecursively();
if ($found) {
    echo "Found banned words in the following files:\n";
    foreach ($found as $info) {
        echo $info['file'] . ' - "' . implode('", "', $info['words']) . "\"\n";
    }
    exit(1);
}

if ($verbose) {
    echo "No banned words found in the source code.\n";
}
exit(0);
