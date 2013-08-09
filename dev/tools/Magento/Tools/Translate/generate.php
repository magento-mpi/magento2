<?php
/**
 * {license_notice}
 *
 * @category   Tools
 * @package    translate
 * @copyright  {copyright}
 * @license    {license_link}
 */

/*
 *
 * php -f generate.php -- --locale en_US --output filename
 *
 * Output file format (CSV):
 * Columns:
 *
 * Module_Name (like 'Magento_Catalog' or design package name like 'translate')
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
    --split     generate csv file with only unique keys with list files
    --xmlonly   parse only xml files
    --xxx       set all translate value as 'Module Name'

USAGE
);

define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', dirname(dirname(dirname(__DIR__))));

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
    die(USAGE . 'Please indicate output parameter' ."\n");
}
if (!is_writeable(dirname($args['output']))) {
    die(USAGE . sprintf('Output dir %s isn\'t writeable', realpath(dirname($args['output']))) ."\n");
}

require_once __DIR__ . '/config.inc.php';
require_once BASE_PATH . DS . 'lib/Varien/File/Csv.php';

$CONFIG['generate'] = array(
    'base_dir'      => BASE_PATH,
    'allow_ext'     => '(php|phtml)',
    'xml_ext'       => '(xml)',
    'xml_ignore'    => array('wsdl.xml', 'wsdl2.xml', 'wsi.xml'),
    'exclude_dirs'  => '(\.svn|sql)',
    'print_dir'     => false,
    'print_file'    => false,
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
 * @param array $exclude
 * @param bool $_isRecursion
 */
function parseDir($path, $basicModuleName, $exclude = array(), $_isRecursion = false)
{
    global $CONFIG;
    static $skipDirs = array();

    if (!file_exists($path)) {
        print (sprintf("Config path not found %s\n", $path));
        return false;
    }

    if (is_file($path)) {
        if ($CONFIG['generate']['print_file']) {
            print 'check file ' . $path . "\n";
        }

        if (preg_match('/\.'.$CONFIG['generate']['allow_ext'].'$/', $path)) {
            parseFile($path, $basicModuleName);
        }
        elseif (preg_match('/\.'.$CONFIG['generate']['xml_ext'].'$/', $path)) {
            parseXmlFile($path, $basicModuleName);
        }
        return true;
    }

    if ($CONFIG['generate']['print_dir']) {
        print 'check dir ' . $path . "\n";
    }

    // skip excluded dirs
    if (!$_isRecursion) {
        foreach ($exclude as $dir) {
            $skipDirs[] = realpath($dir);
        }
    }
    if (in_array(realpath($path), $skipDirs)) {
        return;
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
                $skipDirs[] = realpath($path);
                parseDir($path.$dir_element.DIRECTORY_SEPARATOR, $basicModuleName, $exclude, true);
            }
        }
        unset($dir_element);
        closedir($dirh);
    }

    return true;
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
        /** Mage::helper('helper_name')->__ or $this->helper('helper_name')->__ */
        if (preg_match_all('/helper\(([\'|\\\"])([a-z0-9_]+)(?:\/[a-z0-9_]+)?\\1\)-\>__\([\s]*([\'|\\\"])(.*?[^\\\\])\\3.*?\)/', $fileString, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $k => $match) {
                $CONFIG['generate']['match_helper'] ++;

                if (!isset($CONFIG['helpers'][$match[2]])) {
                    print '    ignore unknown helper ' . $match[2] . "\n";
                    continue;
                }
                $moduleName     = $CONFIG['helpers'][$match[2]];
                $translationKey = $match[4];

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

    if (in_array(basename($fileName), $CONFIG['generate']['xml_ignore'])) {
        return;
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
function xmlFindTranslate($xmlNode, &$translate, $module = null, $xPath = array())
{
    $xPath[] = (string)$xmlNode->getName();
    foreach ($xmlNode as $node) {
        $attributes = $node->attributes();
        $nodeModule = isset($attributes['module']) ? (string)$attributes['module'] : $module;
        if (isset($attributes['translate'])) {
            if (is_numeric($attributes['translate']) || $attributes['translate'] == "true") {
                $translate[] = array(
                    'module' => $nodeModule,
                    'value' => (string) $node,
                    'xpath' => '//' . join('/', $xPath)
                );
            } else {
                $translateNodes = explode(' ', $attributes['translate']);

                foreach ($translateNodes as $nodeName) {
                    if (!(string)$node->$nodeName) {
                        continue;
                    }
                    $translate[] = array(
                        'module'    => $nodeModule,
                        'value'     => (string)$node->$nodeName,
                        'xpath'     => '//' . join('/', $xPath + array($nodeName))
                    );
                }
            }
        }
        xmlFindTranslate($node, $translate, $nodeModule, $xPath);
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
    elseif (isset($CONFIG['generate']['args']['split'])) {
        global $cvsClean;
        $file = $fileName . '(' . $fileLine . ')';
        if (!isset($cvsClean[$moduleName][$translationKey])) {
            $csvCount = count($csvData);
            $csvData[$csvCount]  = array(
                $moduleName,
                $translationKey,
                isset($CONFIG['generate']['args']['xxx']) ? $moduleName . ($xml ? '_XML' : '') : $translationKey,
                array($file)
            );
            $cvsClean[$moduleName][$translationKey] = $csvCount;
        } else {
            $csvData[$cvsClean[$moduleName][$translationKey]][3][] = $file;
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
    // pick folders to exclude
    $exclude = array();
    foreach ($modulePaths as $k => $path) {
        if (0 === strpos($path, '!')) {
            $exclude[] = substr($path, 1);
            unset($modulePaths[$k]);
        }
    }
    // dive into dirs
    foreach ($modulePaths as $path) {
        parseDir($path, $basicModuleName, $exclude);
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
elseif (isset($CONFIG['generate']['args']['split'])) {
    foreach ($csvData as $k => $v) {
        $csvData[$k][3] = join(', ', $v[3]);
    }
    multiSort($csvData, array(0, SORT_ASC), array(1, SORT_ASC), array(2, SORT_ASC), array(3, SORT_ASC));
}
else {
    multiSort($csvData, array(0, SORT_ASC), array(1, SORT_ASC), array(2, SORT_ASC), array(3, SORT_ASC), array(4, SORT_ASC));
}

/** write to file */
$varienCsv = new Magento_File_Csv();
$varienCsv->saveData($args['output'], $csvData);
