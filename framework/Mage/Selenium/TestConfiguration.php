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
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
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
     * Configuration object instance
     * @var Mage_Selenium_TestConfiguration|null
     */
    public static $instance = null;

    /**
     * File helper instance
     * @var Mage_Selenium_Helper_File|null
     */
    protected $_fileHelper = null;

    /**
     * Config helper instance
     * @var Mage_Selenium_Helper_Config|null
     */
    protected $_configHelper = null;

    /**
     * UIMap helper instance
     * @var Mage_Selenium_Helper_Uimap|null
     */
    protected $_uimapHelper = null;

    /**
     * Data helper instance
     * @var Mage_Selenium_Helper_Data|null
     */
    protected $_dataHelper = null;

    /**
     * Params helper instance
     * @var Mage_Selenium_Helper_Params|null
     */
    protected $_paramsHelper = null;

    /**
     * Data generator helper instance
     * @var Mage_Selenium_Helper_DataGenerator|null
     */
    protected $_dataGeneratorHelper = null;

    /**
     * Cache helper instance
     * @var Mage_Selenium_Helper_Cache
     */
    protected $_cacheHelper = null;

    /**
     * Constructor defined as private to implement singleton
     */
    private function __construct()
    {
    }

    /**
     * Get test configuration instance
     *
     * @static
     * @return null
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
            self::$instance->init();
        }
        return self::$instance;
    }

    /**
     * Initializes test configuration instance which includes:
     * <ul>
     * <li>Initialize configuration
     * <li>Initialize Fixtures
     * </ul>
     */
    public function init()
    {
        $this->_initConfig();
        $this->_initFixture();
    }

    /**
     * Initializes and loads configuration data
     * @return Mage_Selenium_TestConfiguration
     */
    protected function _initConfig()
    {
        $this->getHelper('config');
        return $this;
    }

    /**
     * Initializes and loads fixtures data
     * @return Mage_Selenium_TestConfiguration
     */
    protected function _initFixture()
    {
        $this->getHelper('uimap');
        $this->getHelper('data');
        return $this;
    }

    /**
     * Get $helperName helper instance
     *
     * @param string $helperName cache|config|data|dataGenerator|file|params|uimap
     *
     * @return object
     * @throws OutOfRangeException
     */
    public function getHelper($helperName)
    {
        $class = 'Mage_Selenium_Helper_' . ucfirst($helperName);
        if (class_exists($class)) {
            $variableName = '_' . $helperName . 'Helper';
            if (is_null($this->$variableName)) {
                if (strtolower($helperName) !== 'params') {
                    $this->$variableName = new $class($this);
                } else {
                    $this->$variableName = new $class();
                }
            }
            return $this->$variableName;
        }
        throw new OutOfRangeException($class . ' does not exist');
    }

    /**
     * Get node|value by path
     *
     * @param array  $data Array of Configuration|DataSet data
     * @param string $path XPath-like path to Configuration|DataSet value
     *
     * @return array|string|bool
     */
    public function _descend($data, $path)
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
     * Initializes new driver connection with specific configuration
     *
     * @param array $browser
     *
     * @return Mage_Selenium_Driver
     * @throws InvalidArgumentException
     */
    public function addDriverConnection(array $browser)
    {
        if (!isset($browser['name'])) {
            $browser['name'] = '';
        }
        if (!isset($browser['browser'])) {
            $browser['browser'] = '';
        }
        if (!isset($browser['host'])) {
            $browser['host'] = 'localhost';
        }
        if (!isset($browser['port'])) {
            $browser['port'] = 4444;
        }
        if (!isset($browser['timeout'])) {
            $browser['timeout'] = 30;
        }
        if (!isset($browser['httpTimeout'])) {
            $browser['httpTimeout'] = 45;
        }
        if (!isset($browser['restartBrowser'])) {
            $browser['restartBrowser'] = true;
        }
        $driver = new Mage_Selenium_Driver();
        $driver->setName($browser['name']);
        $driver->setBrowser($browser['browser']);
        $driver->setHost($browser['host']);
        $driver->setPort($browser['port']);
        $driver->setTimeout($browser['timeout']);
        $driver->setHttpTimeout($browser['httpTimeout']);
        $driver->setContiguousSession($browser['restartBrowser']);
        $driver->setBrowserUrl($this->_configHelper->getBaseUrl());

        return $driver;
    }
}