<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper providing exclusive restricted access to the underlying bootstrap instance
 */
class Magento_Test_Helper_Bootstrap
{
    /**
     * @var Magento_Test_Helper_Bootstrap
     */
    private static $_instance;

    /**
     * @var Magento_Test_Bootstrap
     */
    protected $_bootstrap;

    /**
     * Set self instance for static access
     *
     * @param Magento_Test_Helper_Bootstrap $instance
     * @throws Magento_Exception
     */
    public static function setInstance(Magento_Test_Helper_Bootstrap $instance)
    {
        if (self::$_instance) {
            throw new Magento_Exception('Helper instance cannot be redefined.');
        }
        self::$_instance = $instance;
    }

    /**
     * Self instance getter
     *
     * @return Magento_Test_Helper_Bootstrap
     * @throws Magento_Exception
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            throw new Magento_Exception('Helper instance is not defined yet.');
        }
        return self::$_instance;
    }

    /**
     * Check the possibility to send headers or to use headers related function (like set_cookie)
     *
     * @return bool
     */
    public static function canTestHeaders()
    {
        if (!headers_sent() && extension_loaded('xdebug') && function_exists('xdebug_get_headers')) {
            return true;
        }
        return false;
    }

    /**
     * Constructor
     *
     * @param Magento_Test_Bootstrap $bootstrap
     */
    public function __construct(Magento_Test_Bootstrap $bootstrap)
    {
        $this->_bootstrap = $bootstrap;
    }

    /**
     * Retrieve application installation directory
     *
     * @return string
     */
    public function getAppInstallDir()
    {
        return $this->_bootstrap->getApplication()->getInstallDir();
    }

    /**
     * Retrieve application initialization options
     *
     * @return array
     */
    public function getAppInitParams()
    {
        return $this->_bootstrap->getApplication()->getInitParams();
    }

    /**
     * Retrieve the database vendor name used by the bootstrap
     *
     * @return string
     */
    public function getDbVendorName()
    {
        return $this->_bootstrap->getDbVendorName();
    }

    /**
     * Reinitialize the application instance optionally passing parameters to be overridden.
     * Intended to be used for the tests isolation purposes.
     *
     * @param array $overriddenParams
     */
    public function reinitialize(array $overriddenParams = array())
    {
        $this->_bootstrap->getApplication()->reinitialize($overriddenParams);
    }

    /**
     * Perform the full request processing by the application instance optionally passing parameters to be overridden.
     * Intended to be used by the controller tests.
     *
     * @param array $overriddenParams
     */
    public function runApp(array $overriddenParams = array())
    {
        $this->_bootstrap->getApplication()->run($overriddenParams);
    }
}