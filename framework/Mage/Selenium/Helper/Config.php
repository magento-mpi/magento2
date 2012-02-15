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
 * Config helper class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_Helper_Config extends Mage_Selenium_Helper_Abstract
{
    /**
     * Configuration data
     * @var array
     */
    protected $_configData = array();

    /**
     * Configuration data for all browsers
     * @var array
     */
    protected $_configBrowsers = array();

    /**
     * Configuration data for framework
     * @var array
     */
    protected $_configFramework = array();

    /**
     * Configuration data for all applications
     * @var array
     */
    protected $_configApplications = array();

    /**
     * Configuration data for all areas for current application
     * @var array
     */
    protected $_configAreas = array();

    /**
     * Default browser name
     * @var string
     */
    const DEFAULT_BROWSER = 'default';

    /**
     * Default application name
     * @var string
     */
    const DEFAULT_APPLICATION = 'default';

    /**
     * Default area
     * @var string
     */
    const DEFAULT_AREA = 'frontend';

    /**
     * Configuration data for current application
     * @var array
     */
    protected $_applicationConfig = array();

    /**
     * Configuration data for the current area
     * @var array
     */
    protected $_areaConfig = array();

    /**
     * Name of the current application
     * @var null|string
     */
    protected $_application = null;

    /**
     * Name of the current area
     * @var null|string
     */
    protected $_area = null;

    /**
     * Name of the current page
     * @var null|string
     */
    protected $_currentPageId = null;

    /**
     * Initialize config
     */
    protected function _init()
    {
        $this->_loadConfigData();
        $this->_loadConfigBrowsers();
        $this->setApplication(self::DEFAULT_APPLICATION);
        $this->setArea(self::DEFAULT_AREA);
        $areaConfig = $this->getAreaConfig();
        $this->setCurrentPageId($areaConfig['base_page_uimap']);
    }

    /**
     * Load Config Data
     * @return Mage_Selenium_Helper_Config
     * @throws OutOfRangeException
     */
    protected function _loadConfigData()
    {
        $files = array('local.yml', 'config.yml');
        foreach ($files as $file) {
            $configDir = implode(DIRECTORY_SEPARATOR, array(SELENIUM_TESTS_BASEDIR, 'config', $file));
            $fileData = $this->getConfig()->getHelper('file')->loadYamlFile($configDir);
            if ($fileData) {
                $this->_configData = $fileData;
                return $this;
            }
        }
        throw new OutOfRangeException('Configuration files do not exist');
    }

    /**
     * Get value from Configuration file
     *
     * @param string $path XPath-like path to config value (by default = '')
     *
     * @return array|string|bool
     */
    protected function getConfigValue($path = '')
    {
        return $this->getConfig()->_descend($this->_configData, $path);
    }

    /**
     * Return config for framework
     * @return array
     */
    public function getConfigFramework()
    {
        if (empty($this->_configFramework)) {
            $this->_configFramework = $this->getConfigValue('framework');
        }
        return $this->_configFramework;

    }

    /**
     * Load config for browsers
     * @return Mage_Selenium_Helper_Config
     */
    protected function _loadConfigBrowsers()
    {
        $config = $this->getConfigValue('browsers');
        if (array_key_exists(self::DEFAULT_BROWSER, $config)) {
            if (is_array($config[self::DEFAULT_BROWSER])) {
                $this->_configBrowsers[self::DEFAULT_BROWSER] = $config[self::DEFAULT_BROWSER];
            } else {
                unset($config[self::DEFAULT_BROWSER]);
                $this->_configBrowsers = $config;
            }
        } else {
            $this->_configBrowsers = $config;
        }
        Mage_Selenium_TestCase::$browsers = $this->_configBrowsers;
        return $this;
    }

    /**
     * Change current application
     *
     * @param string $name application name
     *
     * @return Mage_Selenium_Helper_Config
     * @throws InvalidArgumentException
     */
    public function setApplication($name)
    {
        $config = $this->getConfigApplications();
        if (!isset($config[$name])) {
            throw new InvalidArgumentException('Application with the ' . $name . ' name is absent');
        }
        $this->_applicationConfig = $config[$name];
        $this->_application = $name;

        return $this;
    }

    /**
     * Change current area
     *
     * @param string $name
     *
     * @return Mage_Selenium_Helper_Config
     * @throws OutOfRangeException
     */
    public function setArea($name)
    {
        $config = $this->getConfigAreas();
        if (!isset($config[$name])) {
            throw new OutOfRangeException('Area with the name ' . $name . ' is absent');
        }
        $this->_areaConfig = $config[$name];
        $this->_area = $name;

        return $this;
    }

    /**
     * Change current page
     *
     * @param string $pageId
     */
    public function setCurrentPageId($pageId)
    {
        $this->_currentPageId = $pageId;
    }

    /**
     * Return all application configs
     * @return array
     */
    public function getConfigApplications()
    {
        if (!$this->_configApplications) {
            $this->_configApplications = $this->getConfigValue('applications');
        }
        return $this->_configApplications;
    }

    /**
     * Return all area configs for current application
     * @return array
     * @throws OutOfRangeException
     */
    public function getConfigAreas()
    {
        if (!$this->_configAreas) {
            $config = $this->getApplicationConfig();
            if (!isset($config['areas'])) {
                throw new OutOfRangeException('Areas for "' . $this->_application . '" application is not set');
            }
            $this->_configAreas = $config['areas'];
        }
        return $this->_configAreas;
    }

    /**
     * Return current application config
     * @return array
     * @throws OutOfRangeException
     */
    public function getApplicationConfig()
    {
        if (empty($this->_applicationConfig)) {
            throw new OutOfRangeException('Application Config is not set');
        }
        return $this->_applicationConfig;
    }

    /**
     * Return current application name
     * @return string
     * @throws OutOfRangeException
     */
    public function getApplication()
    {
        if (is_null($this->_application)) {
            throw new OutOfRangeException('Application is not set');
        }
        return $this->_application;
    }

    /**
     * Return current area config
     * @return array
     * @throws OutOfRangeException
     */
    public function getAreaConfig()
    {
        if (empty($this->_areaConfig)) {
            throw new OutOfRangeException('Area Config is not set');
        }
        return $this->_areaConfig;
    }

    /**
     * Return current area name
     * @return string
     * @throws OutOfRangeException
     */
    public function getArea()
    {
        if (is_null($this->_area)) {
            throw new OutOfRangeException('Area is not set');
        }
        return $this->_area;
    }

    /**
     * Return current page name
     * @return string
     */
    public function getCurrentPageId()
    {
        if (is_null($this->_currentPageId)) {
            throw new OutOfRangeException('Current page is not set');
        }
        return $this->_currentPageId;
    }

    /**
     * Return BaseUrl for current area
     * @return string
     * @throws OutOfRangeException
     */
    public function getBaseUrl()
    {
        $config = $this->getAreaConfig();
        if (!isset($config['url'])) {
            throw new OutOfRangeException('Base Url is not set for "' . $this->getArea() . '" area');
        }
        return $config['url'];

    }

    /**
     * Return default Login for current area
     * @return string
     * @throws OutOfRangeException
     */
    public function getDefaultLogin()
    {
        $config = $this->getAreaConfig();
        if (!isset($config['login'])) {
            throw new OutOfRangeException('Login is not set for "' . $this->getArea() . '" area');
        }
        return $config['login'];
    }

    /**
     * Return default Password for current area
     * @return string
     * @throws OutOfRangeException
     */
    public function getDefaultPassword()
    {
        $config = $this->getAreaConfig();
        if (!isset($config['password'])) {
            throw new OutOfRangeException('Password is not set for "' . $this->getArea() . '" area');
        }
        return $config['password'];
    }
}