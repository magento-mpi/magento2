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
class Mage_Selenium_TestConfiguration
{
    /**
     * Data helper instance
     *
     * @var Mage_Selenium_Helper_Data
     */
    protected $_dataHelper = null;

    /**
     * Data generator helper instance
     *
     * @var Mage_Selenium_Helper_DataGenerator
     */
    protected $_dataGenerator = null;

    /**
     * Page helper instance
     *
     * @var Mage_Selenium_Helper_Page
     */
    protected $_pageHelper = null;

    /**
     * File helper instance
     *
     * @var Mage_Selenium_Helper_File
     */
    protected $_fileHelper = null;

    /**
     * Application helper instance
     *
     * @var Mage_Selenium_Helper_Application
     */
    protected $_applicationHelper = null;

    /**
     * Uimap helper instance
     *
     * @var Mage_Selenium_Helper_Uimap
     */
    protected $_uimapHelper = null;

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
     * Confiration object instance
     *
     * @var Mage_Selenium_TestConfiguration
     */
    public static $instance = null;

    /**
     * Test data
     *
     * @var array
     */
    protected $_testData = array();

    /**
     * Configuration data
     *
     * @var array
     */
    protected $_configData = array();

    /**
     * Constructor defined private to implement singleton
     */
    private function __construct()
    {
    }

    /**
     * Destructor
     */
    public function  __destruct()
    {
        if ($this->getConfigValue('browsers/default/doNotKillBrowsers')
            != 'true' && $this->_drivers
        ) {
            foreach ($this->_drivers as $driver) {
                $driver->setContiguousSession(false);
                $driver->stop();
            }
        }
    }

    /**
     * Initializes test configuration
     */
    public static function initInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
            self::$instance->init();
        }
        return self::$instance;
    }

    /**
     * Initializes test configuration instance
     *
     * @return Mage_Selenium_TestConfiguration
     */
    public function init()
    {
        $this->_initConfig();
        $this->_initTestData();
        $this->getUimapHelper();
        $this->_initDrivers();
        return $this;
    }

    /**
     * Retrieve file helper instance
     *
     * @return Mage_Selenium_Helper_File
     */
    public function getFileHelper()
    {
        if (is_null($this->_fileHelper)) {
            $this->_fileHelper = new Mage_Selenium_Helper_File($this);
        }
        return $this->_fileHelper;
    }

    /**
     * Retrieve page helper instance
     *
     * @param Mage_Selenium_TestCase $testCase
     * @param Mage_Selenium_Helper_Application $applicationHelper
     * @return Mage_Selenium_Helper_Page
     */
    public function getPageHelper(Mage_Selenium_TestCase $testCase=null, Mage_Selenium_Helper_Application $applicationHelper=null)
    {
        if (is_null($this->_pageHelper)) {
            $this->_pageHelper = new Mage_Selenium_Helper_Page($this);
        }
        if (!is_null($testCase)) {
            $this->_pageHelper->setTestCase($testCase);
        }
        if (!is_null($applicationHelper)) {
            $this->_pageHelper->setApplicationHelper($applicationHelper);
        }
        return $this->_pageHelper;
    }

    /**
     * Retrieve data generator helper instance
     *
     * @return Mage_Selenium_Helper_DataGenerator
     */
    public function getDataGenerator()
    {
        if (is_null($this->_dataGenerator)) {
            $this->_dataGenerator = new Mage_Selenium_Helper_DataGenerator($this);
        }
        return $this->_dataGenerator;
    }

    /**
     * Retrieve data helper instance
     *
     * @return Mage_Selenium_Helper_Data
     */
    public function getDataHelper()
    {
        if (is_null($this->_dataHelper)) {
            $this->_dataHelper = new Mage_Selenium_Helper_Data($this);
        }
        return $this->_dataHelper;
    }

    /**
     * Retrieve Application helper instance
     *
     * @return Mage_Selenium_Helper_File
     */
    public function getApplicationHelper()
    {
        if (is_null($this->_applicationHelper)) {
            $this->_applicationHelper = new Mage_Selenium_Helper_Application($this);
        }
        return $this->_applicationHelper;
    }

    /**
     * Retrieve uimap helper instance
     *
     * @return Mage_Selenium_Helper_Uimap
     */
    public function getUimapHelper()
    {
        if (is_null($this->_uimapHelper)) {
            $this->_uimapHelper = new Mage_Selenium_Helper_Uimap($this);
        }
        return $this->_uimapHelper;
    }

    /**
     * Initializes configuration
     *
     * @return Mage_Selenium_TestConfiguration
     */
    protected function _initConfig()
    {
        $this->_loadConfigData();
        return $this;
    }

    /**
     * Initializes test data from default location
     *
     * @return Mage_Selenium_TestConfiguration
     */
    protected function _initTestData()
    {
        $this->_loadTestData();
        return $this;
    }

    /**
     * Initializes all driver connections from configuration
     *
     * @return Mage_Selenium_TestConfiguration
     */
    protected function _initDrivers()
    {
        $connections = $this->getConfigValue('browsers');
        foreach ($connections as $connection => $config) {
            $this->_addDriverConnection($config);
        }
        return $this;
    }

    /**
     * Initializes new driver connection
     *
     * @param array $connectionConfig
     * @return Mage_Selenium_TestConfiguration
     */
    protected function _addDriverConnection(array $connectionConfig)
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
    }

    /**
     * Retrieve value from configuration
     *
     * @param string $path - xpath-like path to config value
     * @return array
     */
    public function getConfigValue($path = '')
    {
        return $this->_descend($this->_configData, $path);
    }

    /**
     * Retrieve value from data configuration by path
     *
     * @param string $path
     * @return array|string
     */
    public function getDataValue($path = '')
    {
        return $this->_descend($this->_testData, $path);
    }

    /**
     * Get node|value by path
     *
     * @param array $config
     * @param string $path
     * @return array|string
     */
    protected function _descend($data, $path)
    {
        $pathArr = (!empty($path)) ? explode('/', $path) : '';
        $currNode = $data;
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
     * Load and merge data files
     *
     * @return Mage_Selenium_TestConfiguration
     */
    protected function _loadTestData()
    {
        $files = SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'data'
                . DIRECTORY_SEPARATOR . '*.yml';
        $this->_testData = $this->getFileHelper()->loadYamlFiles($files);
        return $this;
    }

    /**
     * Load configuration data
     *
     * @return Mage_Selenium_TestConfiguration
     */
    protected function _loadConfigData()
    {
        $files = array(
            'browsers.yml',
            'local.yml'
        );
        $configDir = SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR;
        foreach ($files as $file) {
            $fileData = $this->getFileHelper()->loadYamlFile($configDir . $file);
            if ($fileData) {
                $this->_configData = array_replace_recursive($this->_configData, $fileData);
            }
        }
        return $this;
    }

}
