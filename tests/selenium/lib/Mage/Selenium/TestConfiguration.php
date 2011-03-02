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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test run configuration
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
require_once('SymfonyComponents/YAML/sfYaml.php');

class Mage_Selenium_TestConfiguration
{

    /**
     * Data helper instance
     *
     * @var Mage_Selenium_DataHelper
     */
    public $dataHelper = null;

    /**
     * Data generator instance
     *
     * @var Mage_Selenium_DataGenerator
     */
    public $dataGenerator = null;

    public $pageHelper = null;

    /**
     * Initialized browsers connections
     * @var array[int]PHPUnit_Extensions_SeleniumTestCase_Driver
     */
    protected $_drivers = array();

    /**
     * Current browser connection
     *
     * @var PHPUnit_Extensions_SeleniumTestCase_Driver
     */
    public $driver = null;

    /**
     * Configuration obejct instance
     * @var Mage_Selenium_TestConfiguration
     */
    public static $instance = null;

    /**
     * Constructor defined private to implement singleton
     */
    private function __construct()
    {
    }

    /**
     * Data configuration
     * 
     * @var array
     */
    protected static $_data = array();

    /**
     * Uimap data configuration
     *
     * @var array
     */
    protected static $_uimapData = array();

    /**
     * Loaded configuration files
     *
     * @var array[filename]Mage_Selenium_YamlConfig
     */
    protected static $_configs = array();

    /**
     * Initializes test configuration
     */
    public static function init()
    {
        $instance = new self();
        self::$instance = $instance;
        $instance->dataHelper = new Mage_Selenium_DataHelper($instance);
        $instance->dataGenerator = new Mage_Selenium_DataGenerator($instance);
        $instance->pageHelper = new Mage_Selenium_PageHelper($instance);

        // @TODO load from configuration
        $connectionConfig = array(
            'browser'   => '*chrome',
            'host'      => '127.0.0.1',
            'port'      => 5555,
        );
        $instance->initDriver($connectionConfig);
    }

    /**
     * Get page helper instance
     *
     * @param Mage_Selenium_TestCase $testCase
     * @return Mage_Selenium_PageHelper
     */
    public function getPageHelper(Mage_Selenium_TestCase $testCase)
    {
        $this->pageHelper->setTestCase($testCase);
        return $this->pageHelper;
    }

    /**
     *  Initialize new driver connection
     *
     * @param array $connectionConfig
     * @return Mage_Selenium_TestConfiguration
     */
    public function initDriver(array $connectionConfig)
    {
        $driver = new Mage_Selenium_Driver();
        $driver->setBrowser($connectionConfig['browser']);
        $driver->setHost($connectionConfig['host']);
        $driver->setPort($connectionConfig['port']);
        $driver->setContiguousSession(true);
        $this->_drivers[] = $driver;
        // @TODO implement interations outside
        $this->driver = $this->_drivers[0];
        return $this;
        //var_dump(self::getConfig('browsers', 'browsers/firefox36/browser'));
        //var_dump(self::getData('product_attribute_textfield/attribute_code'));
        //var_dump(self::getUimapData('frontend/customer_account_create/title'));

    }

    /**
     * Retrieve value from configuration
     *
     * @param string $file - filename without an extension (.yml is the default filename extension)
     * @param string $path - xpath-like path to config value
     * @return array
     */
    public static function getConfig($file, $path = '')
    {
        if (!isset(self::$_configs[$file])) {
            $filename = SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $file . '.yml';

            if ($file && file_exists($filename)) {
                $config = self::$_configs[$file] = sfYaml::load($filename);
            } else {
                return false;
            }
        } else {
            $config = self::$_configs[$file];
        }

        return self::_descend($config, $path);
    }

    /**
     * Get node|value by path
     *
     * @param array $config
     * @param string $path
     * @return array|string
     */
    protected static function _descend($config, $path)
    {
        $pathArr = (!empty($path)) ? explode('/', $path) : '';

        $currNode = $config;
        if (!empty($pathArr)) {
            foreach ($pathArr as $node) {
                if (isset($currNode[$node])) {
                    $currNode = $currNode[$node];
                } else {
                    return false;
                }
            }
        }

        return $currNode;
    }

    /**
     * Retrieve value from data configuration by path
     * 
     * @param string $path
     * @return array|string
     */
    public static function getData($path = '')
    {
        if (empty(self::$_data)) {
            self::_loadData();
        }
        
        return self::_descend(self::$_data, $path);
    }

    /**
     * Load and merge data files
     */
    protected static function _loadData()
    {
        $dataDir = SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'data';
        $data = array();
        
        $files = glob($dataDir . DIRECTORY_SEPARATOR . '*.yml');

        if (!empty($files)) {
            foreach ($files as $file) {
                if (is_readable($file)) {
                    $fileData = sfYaml::load($file);
                    if (is_array($fileData)) {
                        $data = self::arrayMerging($data, $fileData);
                    }
                }
            }
        }

        self::$_data = $data;
    }


    /**
     * Retrieve value from uimap data configuration by path
     *
     * @param string $path
     * @return array|string
     */
    public static function getUimapData($path = '')
    {
        if (empty(self::$_uimapData)) {
            self::_loadUimapData('admin');
            self::_loadUimapData('frontend');
        }

        return self::_descend(self::$_uimapData, $path);
    }

    /**
     * Load and merge data files
     *
     * @param string $area 'admin'|'frontend'
     */
    protected static function _loadUimapData($area)
    {
        $dataDir = SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'uimaps' . DIRECTORY_SEPARATOR . $area;
        $uimapsData[$area] = array();

        $files = glob($dataDir . DIRECTORY_SEPARATOR . '*.yml');

        if (!empty($files)) {
            foreach ($files as $file) {
                if (is_readable($file)) {
                    $fileData = sfYaml::load($file);
                    $uimapsData[$area] = self::arrayMerging($uimapsData[$area], $fileData);
                }
            }
        }

        self::$_uimapData[$area] = $uimapsData[$area];
    }

    /**
     * Merge configuration arrays
     * 
     * @return array
     */
    protected static function arrayMerging()
    {
        if (function_exists('array_replace_recursive')) {
            // native function is used (version >= 5.3.0)
            $array = call_user_func_array('array_replace_recursive', func_get_args());
        } else {
            // own merging function is used
            $args = func_get_args();
            if (isset($args[0]) && is_array($args[0])) {
                $array = $args[0];
                
                for ($i = 1; $i < func_num_args(); $i++) {
                    if (is_array($args[$i])) $array = self::_arrayReplaceRecursive($array, $args[$i]);
                }
            } else {
                $array = false;
            }
        }
        
        return $array;
    }

    
    /**
     * Merge two arrays, array_replace_recursive implementation
     *
     * @param array $array
     * @param array $array1
     * @return array
     */
    protected static function _arrayReplaceRecursive($array, $array1)
    {
        if (!empty($array1) && is_array($array1)) {
            foreach ($array1 as $key => $value) {
                if (!isset($array[$key]) || (isset($array[$key]) && !is_array($array[$key]))) {
                    $array[$key] = array();
                }

                if (is_array($value)) { 
                    $value = self::_arrayReplaceRecursive($array[$key], $value);
                }
                
                $array[$key] = $value;
            }
        }
        
        return $array;
    }
}
