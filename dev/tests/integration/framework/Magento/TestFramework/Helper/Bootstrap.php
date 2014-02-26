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
namespace Magento\TestFramework\Helper;

class Bootstrap
{
    /**
     * @var \Magento\TestFramework\Helper\Bootstrap
     */
    private static $_instance;

    /**
     * @var \Magento\ObjectManager
     */
    private static $_objectManager;

    /**
     * @var \Magento\TestFramework\Bootstrap
     */
    protected $_bootstrap;

    /**
     * Set self instance for static access
     *
     * @param \Magento\TestFramework\Helper\Bootstrap $instance
     * @throws \Magento\Exception
     */
    public static function setInstance(\Magento\TestFramework\Helper\Bootstrap $instance)
    {
        if (self::$_instance) {
            throw new \Magento\Exception('Helper instance cannot be redefined.');
        }
        self::$_instance = $instance;
    }

    /**
     * Self instance getter
     *
     * @return \Magento\TestFramework\Helper\Bootstrap
     * @throws \Magento\Exception
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            throw new \Magento\Exception('Helper instance is not defined yet.');
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
     * @param \Magento\TestFramework\Bootstrap $bootstrap
     */
    public function __construct(\Magento\TestFramework\Bootstrap $bootstrap)
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
     * Perform the full request processing by the application instance.
     * Intended to be used by the controller tests.
     */
    public function runApp()
    {
        $this->_bootstrap->getApplication()->run();
    }

    /**
     * Retrieve object manager
     *
     * @return \Magento\ObjectManager
     */
    public static function getObjectManager()
    {
        return self::$_objectManager;
    }

    /**
     * Set object manager
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public static function setObjectManager(\Magento\ObjectManager $objectManager)
    {
        self::$_objectManager = $objectManager;
    }

    /**
     * Get bootstrap object
     *
     * @return \Magento\TestFramework\Bootstrap
     */
    public function getBootstrap()
    {
        return $this->_bootstrap;
    }
}
