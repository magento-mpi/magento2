#!/usr/bin/php
<?php
/**
 * Command line tool for cleaning up Magento source code files.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   build
 * @package    extruder
 * @copyright  Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require dirname(__FILE__) . '/Routine.php';

define('USAGE', <<<USAGE
$>./extruder.php -l common.txt [[-l extra.txt] parameters]
    additional parameters:
    -s vcs_name use "svn rm" command instead of "rm -rf" if the value is "svn" and "git rm" if the value is "git"
    -w dir      use specified working dir instead of current
    -v          verbose output
    -i          ignore errors from remove command

USAGE
);

$shortOpts = 'l:s:w:vi';
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
    $workingDir = rtrim($options['w'], DIRECTORY_SEPARATOR);
}
if (!is_dir($workingDir)) {
    print 'Working dir "' . $workingDir . '" does not exist' . "\n";
    exit(1);
}

$rmCommand = 'rm -rf';
if (isset($options['s'])) {
    $vcsName = $options['s'];
    if ($vcsName == 'svn') {
        $rmCommand = 'svn rm --force';
    } elseif ($vcsName == 'git') {
        $rmCommand = 'git rm -r --ignore-unmatch';
    } else {
        print USAGE;
        exit(1);
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

foreach ($list as $item) {
    if (empty($item)) {
        continue;
    }
    $item = $workingDir . DIRECTORY_SEPARATOR . $item;
    $result = Routine::execCmd("$rmCommand $item", $verbose, $ignore);
    if ($result !== 0) {
        exit($result);
    }
}

exit(0);
