<?php

/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Application helper
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_Helper_Application extends Mage_Selenium_Helper_Abstract
{

    /**
     * Current application area
     *
     * @var string
     */
    protected $_area = '';

    /**
     * Application information:
     * array(
     *      'frontendUrl'   => '',
     *      'adminUrl'      => '',
     *      'adminLogin'    => '',
     *      'adminPassword' => '',
     *      'basePath'      => ''
     * )
     *
     * @var array
     */
    protected $_appInfo = array();

    /**
     * Set current application area
     *
     * @param string $area Possible values are 'frontend' and 'admin'
     *
     * @return Mage_Selenium_Helper_Application
     */
    public function setArea($area)
    {
        if (!in_array($area, array('admin', 'frontend'))) {
            throw new OutOfRangeException();
        }
        $this->_area = $area;
        return $this;
    }

    /**
     * Get current application area
     *
     * @return string
     */
    public function getArea()
    {
        return $this->_area;
    }

    /**
     * Get base url of current area
     *
     * @return string
     */
    public function getBaseUrl()
    {
        $url = $this->isAdmin()
                ? $this->_appInfo['adminUrl']
                : $this->_appInfo['frontendUrl'];
        return $url;
    }

    /**
     * Get base path for application
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->_appInfo['basePath'];
    }

    /**
     * Change Application information
     *
     * @param string $configName
     */
    public function changeAppInfo($configName)
    {
        $applications = $this->_config->getConfigValue('applications');
        $this->_appInfo = $applications[$configName];
        /* Prepend a relative base path with the tests base dir */
        if (!preg_match('#^(?:/|[a-zA-Z]\:[\\\\/])#', $this->_appInfo['basePath'])) {
            $this->_appInfo['basePath'] = SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . $this->_appInfo['basePath'];
        }
    }

    /**
     * Initializes Application information
     *
     * @return Mage_Selenium_Helper_Application
     */
    protected function _init()
    {
        $this->changeAppInfo('default');
        return parent::_init();
    }

    /**
     * Checks if the current area is admin
     *
     * @return boolean
     */
    public function isAdmin()
    {
        if ('admin' == $this->_area) {
            return true;
        }
        return false;
    }

    /**
     * Retrieve default admin user username
     *
     * @return string
     */
    public function getDefaultAdminUsername()
    {
        return $this->_appInfo['adminLogin'];
    }

    /**
     * Retrieve default admin user password
     *
     * @return string
     */
    public function getDefaultAdminPassword()
    {
        return $this->_appInfo['adminPassword'];
    }

}
