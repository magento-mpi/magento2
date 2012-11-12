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
$includePath = array(
    __DIR__,
    realpath(__DIR__ . '/../../../tests/static/framework')
);
set_include_path(implode(PATH_SEPARATOR, $includePath));

spl_autoload_register(function ($class) {
    $file = str_replace('_', '/', $class) . '.php';
    require_once $file;
});

define('USAGE', <<<USAGE
php -f sanity.php -c <config_file> [-w <dir>] [-v]
    -c <config_file> path to configuration file with rules and white list
    [-w <dir>]       use specified working dir instead of current
    [-v]             verbose mode
USAGE
);

$shortOpts = 'c:w:v';
$options = getopt($shortOpts);

if (!isset($options['c'])) {
    print USAGE;
    exit(1);
}
$configFile = $options['c'];

$workingDir = __DIR__;
if (isset($options['w'])) {
    $workingDir = $options['w'];
}

$sanityChecker = new SanityRoutine($configFile, $workingDir);

$verbose = isset($options['v']) ? true : false;
if ($verbose) {
    $words = $sanityChecker->getWords();
    printf('Searching for banned words: "%s"...', implode('", "', $words));
}

$found = $sanityChecker->findWords();
if ($found) {
    echo "Found banned words in the following files:\n";
    foreach ($found as $info) {
        echo $info['file'] . ' - "' . implode('", "', $info['words']) . "\"\n";
    }
    exit(1);
}

if ($verbose) {
    "No banned words found in the source code.\n";
}
exit(0);
