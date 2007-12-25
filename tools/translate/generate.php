<?php
/**
 * Magento
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
 * @category   Mage
 * @package    tools
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/*
 *
 * php -f generate.php -- --locale en_US --output filename
 *
 * Output file format (CSV):
 * Columns:
 *
 * Module_Name (like 'Mage_Catalog' or design package name like 'translate')
 * Translation Key (like "Translate Me")
 * Translation Value (the same)
 * Source File (source file name)
 * Line # (line #)
 *
 * Patterns:
 *
 * Mage::helper('helper_name') => Module_Name
 * $this->__() => Used Module Name if found setUsedModuleName('name') in file, otherwise use Module_Name from config
 * __() => translate
 *
 */

define('USAGE', <<<USAGE
$>php -f generate.php -- --output translateFile.csv

USAGE
);

$args       = array();
$argCurrent = null;
foreach ($_SERVER['argv'] as $arg) {
    if (preg_match('/^--(.*)$/', $arg, $match)) {
        $argCurrent = $match[1];
        $args[$argCurrent] = null;
    }
    else {
        if ($argCurrent) {
            $args[$argCurrent] = $arg;
        }
    }
}

if (!isset($args['output'])) {
    die(USAGE . 'Please indicate output parametr' ."\n");
}
if (!is_writeable(dirname($args['output']))) {
    die(USAGE . sprintf('Output dir %s isn\'t writeable', realpath(dirname($args['output']))) ."\n");
}

require_once 'config.inc.php';
require_once '../../lib/Varien/File/Csv.php';

$CONFIG['generate'] = array(
    'base_dir'      => '../../',
    'allow_ext'     => '(php)',
    'print_dir'     => true,
    'print_file'    => true,
    'print_match'   => false,
    'parse_file'    => 0,
    'match_helper'  => 0,
    'match_this'    => 0,
    'match___'      => 0
);
$csvData    = array();

/**
 * @desc array alternate multisort function
 * [array(key, [SORT_ASC | SORT_DESC])]
 * @param array $array the array
 * @param array $args  the sort option
**/
function multiSort (&$array)
{
    $args   = func_get_args();
    $code   = "";
    for ($i = 1; $i < count($args); $i ++) {
        $j = $args[$i];
        if (is_array($j) && in_array($j[1], array(SORT_ASC, SORT_DESC))) {
            $code .= 'if ($a["'.$j[0].'"] != $b["'.$j[0].'"]) {';
            if ($j[1] == SORT_ASC) {
                $code .= 'return ($a["'.$j[0].'"] < $b["'.$j[0].'"] ? -1 : 1); }';
            }
            else {
                $code .= 'return ($a["'.$j[0].'"] < $b["'.$j[0].'"] ? 1 : -1); }';
            }
        }
    }
    $code .= 'return 0;';

    $cmp = create_function('$a, $b', $code);
    uasort($array, $cmp);
}

/**
 * Parse Directory
 *
 * @param string $path
 * @param string $basicModuleName
 */
function parseDir($path, $basicModuleName)
{
    global $CONFIG;

    if ($CONFIG['generate']['print_dir']) {
        print 'check dir ' . $path . "\n";
    }

    $dirh = opendir($path);
    if ($dirh) {
        while ($dir_element = readdir($dirh)) {
            if ($dir_element == '.' || $dir_element == '..') {
                continue;
            }
            if (preg_match('/\.'.$CONFIG['generate']['allow_ext'].'$/', $dir_element)) {
                parseFile($path.$dir_element, $basicModuleName);
            }
            elseif (is_dir($path.$dir_element) && $dir_element != '.svn') {
                parseDir($path.$dir_element.chr(47), $basicModuleName);
            }
        }
        unset($dir_element);
        closedir($dirh);
    }
}

/**
 * Parse file and find translate
 *
 * @param string $fileName
 * @param string $basicModuleName
 */
function parseFile($fileName, $basicModuleName)
{
    global $CONFIG;

    if ($CONFIG['generate']['print_file']) {
        print '    parse file ' . $fileName . "\n";
    }

    $CONFIG['generate']['parse_file'] ++;

    foreach (file($fileName) as $fileLine => $fileString) {
        /** Mage::helper('helper_name')->__ */
        if (preg_match_all('/Mage\:\:helper\(\\\'([a-z_]+)\\\'\)-\>__\([\s]*([\'|\\\"])(.*?[^\\\\])\\2.*?\)/', $fileString, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $k => $match) {
                $CONFIG['generate']['match_helper'] ++;

                $moduleName     = $CONFIG['helpers'][$match[1]];
                $translationKey = $match[3];

                writeToCsv($moduleName, $translationKey, $fileName, $fileLine);
            }
        }
        /** $this->__ */
        if (preg_match_all('/\$this-\>__\([\s]*([\'|\\\"])(.*?[^\\\\])\\1.*?\)/', $fileString, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $k => $match) {
                $CONFIG['generate']['match_this'] ++;

                $moduleName     = $basicModuleName;
                $translationKey = $match[2];

                writeToCsv($moduleName, $translationKey, $fileName, $fileLine);
            }
        }
        /** __ */
        if (preg_match_all('/[^-][^>]__\([\s]*([\'|\\\"])(.*?[^\\\\])\\1.*?\)/', $fileString, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $k => $match) {
                $CONFIG['generate']['match___'] ++;

                $moduleName     = $basicModuleName;
                $translationKey = $match[2];

                writeToCsv($moduleName, $translationKey, $fileName, $fileLine);
            }
        }
    }
}


/**
 * add data to csv array
 *
 * @param string $moduleName
 * @param string $translationKey
 * @param string $fileName
 * @param string $fileLine
 */
function writeToCsv($moduleName, $translationKey, $fileName, $fileLine)
{
    global $csvData;

    $csvData[]  = array(
        $moduleName,
        $translationKey,
        $translationKey,
        $fileName,
        $fileLine
    );
}

chdir($CONFIG['generate']['base_dir']);

foreach ($CONFIG['translates'] as $basicModuleName => $modulePaths) {
    foreach ($modulePaths as $path) {
        parseDir($path, $basicModuleName);
    }
}

print sprintf("\nParsed %d file(s)\n- Found %d helpers\n- Found %d module this\n- Found %d __ calls\n",
    $CONFIG['generate']['parse_file'],
    $CONFIG['generate']['match_helper'],
    $CONFIG['generate']['match_this'],
    $CONFIG['generate']['match___']
);

multiSort($csvData, array(0, SORT_ASC), array(1, SORT_ASC), array(2, SORT_ASC), array(3, SORT_ASC), array(4, SORT_ASC));

/** write to file */
$varienCsv = new Varien_File_Csv();
$varienCsv->saveData($args['output'], $csvData);