#!/usr/bin/php
<?php
/**
 * {license_notice}
 *
 * @category   build
 * @package    extruder
 * @copyright  {copyright}
 * @license    {license_link}
 */

require dirname(__FILE__) . '/Routine.php';

define('USAGE', <<<USAGE
$>./extruder.php -l common.txt [[-l extra.txt] parameters]
    additional parameters:
    -w dir      use specified working dir instead of current
    -g          use "git rm" command instead of "rm -rf"
    -d          remove in dry-run mode (available for "git rm" command only)
    -v          verbose output
    -i          ignore errors from remove command

USAGE
);

$shortOpts = 'l:w:gdvi';
$options = getopt($shortOpts);

if (!isset($options['l'])) {
    print USAGE;
    exit(1);
}

if (!is_array($options['l'])) {
    $options['l'] = array($options['l']);
}

$list = array();
foreach ($options['l'] as $file) {
    if (!file_exists($file)) {
        print 'File "' . $file . '" does not exist (current dir is "' . getcwd() . '").' . "\n";
        exit(1);
    }
    $list = array_merge($list, explode("\n", file_get_contents($file)));
}
$list = array_unique($list);
foreach ($list as $key => $line) {
    if (trim($line) == '' || substr($line, 0, 2) == '//') {
        unset($list[$key]);
    }
}

$workingDir = '.';
if (isset($options['w'])) {
    $workingDir = realpath(
        rtrim(
            str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $options['w']),
            DIRECTORY_SEPARATOR
        )
    );
}
if (!is_dir($workingDir)) {
    print 'Working dir "' . $workingDir . '" does not exist' . "\n";
    exit(1);
}

$rmCommand = 'rm -rf';
if (isset($options['g'])) {
    $rmCommand = 'git rm -r -f --ignore-unmatch';
    if (isset($options['d'])) {
        $rmCommand .= " --dry-run";
    }
}

$verbose = false;
if (isset($options['v'])) {
    $verbose = true;
}

$ignore = false;
if (isset($options['i'])) {
    $ignore = true;
}

$currentWorkingDir = getcwd();
chdir($workingDir);
foreach ($list as $item) {
    if (empty($item)) {
        continue;
    }
    foreach (Routine::parsePath($item) as $currItem) {
        $currItem = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $currItem);
        $result = Routine::execCmd("$rmCommand $currItem", $verbose, $ignore);
        if ($result !== 0) {
            chdir($currentWorkingDir);
            exit($result);
        }
    }
}
chdir($currentWorkingDir);

exit(0);
