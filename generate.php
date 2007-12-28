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
    additional paramerts:
    --clean     generate csv file with only unique keys
    --xmlonly   parse only xml files
    --xxx       set all translate value as 'Module Name'

USAGE
);

$args       = array();
$argCurrent = null;
foreach ($_SERVER['argv'] as $arg) {
    if (preg_match('/^--(.*)$/', $arg, $match)) {
        $argCurrent = $match[1];
        $args[$argCurrent] = true;
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
    'allow_ext'     => '(php|phtml)',
    'xml_ext'       => '(xml)',
    'exclude_dirs'  => '(\.svn|sql)',
    'print_dir'     => true,
    'print_file'    => true,
    'print_match'   => false,
    'parse_file'    => 0,
    'match_helper'  => 0,
    'match_this'    => 0,
    'match___'      => 0,
    'match_xml'     => 0,
    'args'          => $args
);
$csvData    = array();
$cvsClean   = array();

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
            elseif (preg_match('/\.'.$CONFIG['generate']['xml_ext'].'$/', $dir_element)) {
                parseXmlFile($path.$dir_element, $basicModuleName);
            }
            elseif (is_dir($path.$dir_element) && $dir_element != '.svn' && $dir_element != 'sql') {
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

    if (isset($CONFIG['generate']['args']['xmlonly'])) {
        return ;
    }
    if ($CONFIG['generate']['print_file']) {
        print '    parse file ' . $fileName . "\n";
    }

    $CONFIG['generate']['parse_file'] ++;

    foreach (file($fileName) as $fileLine => $fileString) {
        if (preg_match('/setUsedModuleName\(\\\'([a-zA-Z_]+)\\\'\)/', $fileString, $match)) {
            if (isset($CONFIG['translates'][$match[1]])) {
                $basicModuleName = $match[1];
            }
        }
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

                $moduleName     = 'translate';
                $translationKey = $match[2];

                writeToCsv($moduleName, $translationKey, $fileName, $fileLine);
            }
        }
    }
}

/**
 * Parse XML file
 *
 * @param string $fileName
 * @param string $basicModuleName
 */
function parseXmlFile($fileName, $basicModuleName)
{
    global $CONFIG;

    if ($CONFIG['generate']['print_file']) {
        print '    parse file ' . $fileName . "\n";
    }

    $CONFIG['generate']['parse_file'] ++;

    $xmlContent = file_get_contents($fileName);
    $xmlData = new SimpleXMLElement($xmlContent);
    $xmlTranslate = array();
    xmlFindTranslate($xmlData, $xmlTranslate);
    foreach ($xmlTranslate as $translate) {
        $CONFIG['generate']['match_xml'] ++;

        $moduleName     = $translate['module']
            ? (isset($CONFIG['helpers'][$translate['module']])
                ? $CONFIG['helpers'][$translate['module']]
                : '!' . $translate['module'])
            : $basicModuleName;
        $translationKey = $translate['value'];
        $fileLine       = $translate['xpath'];

        writeToCsv($moduleName, $translationKey, $fileName, $fileLine, true);
    }
}

/**
 * Find attribute translate in SimpleXmlElement object
 *
 * @param SimpleXMLElement $xmlNode
 * @param array $translate
 * @param array $xPath
 */
function xmlFindTranslate($xmlNode, &$translate, $xPath = array())
{
    $xPath[] = (string)$xmlNode->getName();
    foreach ($xmlNode as $node) {
        $attributes = $node->attributes();
        if (isset($attributes['translate'])) {
            $module = isset($attributes['module']) ? (string)$attributes['module'] : null;
            $translateNodes = split(' ', $attributes['translate']);

            foreach ($translateNodes as $nodeName) {
                $translate[] = array(
                    'module'    => $module,
                    'value'     => (string)$node->$nodeName,
                    'xpath'     => '//' . join('/', $xPath + array($nodeName))
                );
            }
        }
        xmlFindTranslate($node, $translate, $xPath);
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
function writeToCsv($moduleName, $translationKey, $fileName, $fileLine, $xml = false)
{
    global $CONFIG, $csvData;

    if (isset($CONFIG['generate']['args']['clean'])) {
        global $cvsClean;
        if (!isset($cvsClean[$moduleName][$translationKey])) {
            $csvData[]  = array(
                $moduleName,
                $translationKey,
                isset($CONFIG['generate']['args']['xxx']) ? $moduleName . ($xml ? '_XML' : '') : $translationKey
            );
            $cvsClean[$moduleName][$translationKey] = true;
        }
    }
    else {
        $csvData[]  = array(
            $moduleName,
            $translationKey,
            $translationKey,
            $fileName,
            $fileLine
        );
    }
}

chdir($CONFIG['generate']['base_dir']);

foreach ($CONFIG['translates'] as $basicModuleName => $modulePaths) {
    foreach ($modulePaths as $path) {
        parseDir($path, $basicModuleName);
    }
}

print sprintf("\nParsed %d file(s)\n- Found %d helpers\n- Found %d module this\n- Found %d __ calls\n- Found %d translate attributes in xml\n",
    $CONFIG['generate']['parse_file'],
    $CONFIG['generate']['match_helper'],
    $CONFIG['generate']['match_this'],
    $CONFIG['generate']['match___'],
    $CONFIG['generate']['match_xml']
);

if (isset($CONFIG['generate']['args']['clean'])) {
    multiSort($csvData, array(0, SORT_ASC), array(1, SORT_ASC), array(2, SORT_ASC));
}
else {
    multiSort($csvData, array(0, SORT_ASC), array(1, SORT_ASC), array(2, SORT_ASC), array(3, SORT_ASC), array(4, SORT_ASC));
}

/** write to file */
$varienCsv = new Varien_File_Csv();
$varienCsv->saveData($args['output'], $csvData);