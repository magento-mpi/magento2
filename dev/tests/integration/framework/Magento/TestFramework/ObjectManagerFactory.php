<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\DriverPool;

/**
 * Class ObjectManagerFactory
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ObjectManagerFactory extends \Magento\Framework\App\ObjectManagerFactory
{
    /**
     * Locator class name
     *
     * @var string
     */
    protected $_locatorClassName = 'Magento\TestFramework\ObjectManager';

    /**
     * Config class name
     *
     * @var string
     */
    protected $_configClassName = 'Magento\TestFramework\ObjectManager\Config';

    /**
     * @var array
     */
    protected $_primaryConfigData = null;

    /**
     * Proxy over arguments instance, used by the application and all the DI stuff
     *
     * @var App\Arguments\Proxy
     */
    protected $appArgumentsProxy;

    /**
     * Override the parent method and return proxied instance instead, so that we can reset the actual app arguments
     * instance for all its clients at any time
     *
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param array $arguments
     * @return App\Arguments\Proxy
     * @throws \Magento\Framework\Exception
     */
    protected function createAppArguments(
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        array $arguments
    ) {
        if ($this->appArgumentsProxy) {
            // Framework constraint: this is ambiguous situation, because it is not clear what to do with older instance
            throw new \Magento\Framework\Exception('Only one creation of application arguments is supported');
        }
        $appArguments = parent::createAppArguments($directoryList, $arguments);
        $this->appArgumentsProxy = new App\Arguments\Proxy($appArguments);
        return $this->appArgumentsProxy;
    }

    /**
     * Restore locator instance
     *
     * @param ObjectManager $objectManager
     * @param string $rootDir
     * @param array $arguments
     * @return ObjectManager
     */
    public function restore(ObjectManager $objectManager, $rootDir, array $arguments)
    {
        $directories = isset($arguments[DirectoryList::INIT_PARAM_PATHS])
            ? $arguments[DirectoryList::INIT_PARAM_PATHS]
            : array();
        $directoryList = new DirectoryList($rootDir, $directories);

        \Magento\TestFramework\ObjectManager::setInstance($objectManager);

        $objectManager->configure($this->_primaryConfigData);
        $objectManager->addSharedInstance($directoryList, 'Magento\Framework\App\Filesystem\DirectoryList');
        $objectManager->addSharedInstance($directoryList, 'Magento\Framework\Filesystem\DirectoryList');

        $appArguments = parent::createAppArguments($directoryList, $arguments);
        $this->appArgumentsProxy->setSubject($appArguments);
        $this->factory->setArguments($appArguments->get());
        $objectManager->addSharedInstance($appArguments, 'Magento\Framework\App\Arguments');

        $objectManager->get('Magento\Framework\Interception\PluginList')->reset();
        $objectManager->configure(
            $objectManager->get('Magento\Framework\App\ObjectManager\ConfigLoader')->load('global')
        );

        return $objectManager;
    }

    /**
     * Load primary config
     *
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param DriverPool $driverPool
     * @param mixed $argumentMapper
     * @param string $appMode
     * @return array
     */
    protected function _loadPrimaryConfig(DirectoryList $directoryList, $driverPool, $argumentMapper, $appMode)
    {
        if (null === $this->_primaryConfigData) {
            $this->_primaryConfigData = array_replace(
                parent::_loadPrimaryConfig($directoryList, $driverPool, $argumentMapper, $appMode),
                array(
                    'default_setup' => array('type' => 'Magento\TestFramework\Db\ConnectionAdapter')
                )
            );
            $this->_primaryConfigData['preferences'] = array_replace(
                $this->_primaryConfigData['preferences'],
                [
                    'Magento\Framework\Stdlib\CookieManager' => 'Magento\TestFramework\CookieManager',
                    'Magento\Framework\ObjectManager\DynamicConfigInterface' =>
                        '\Magento\TestFramework\ObjectManager\Configurator',
                    'Magento\Framework\Stdlib\Cookie' => 'Magento\TestFramework\Cookie',
                    'Magento\Framework\App\RequestInterface' => 'Magento\TestFramework\Request',
                    'Magento\Framework\App\Request\Http' => 'Magento\TestFramework\Request',
                    'Magento\Framework\App\ResponseInterface' => 'Magento\TestFramework\Response',
                    'Magento\Framework\App\Response\Http' => 'Magento\TestFramework\Response',
                    'Magento\Framework\Interception\PluginList' => 'Magento\TestFramework\Interception\PluginList',
                    'Magento\Framework\Interception\ObjectManager\Config' =>
                        'Magento\TestFramework\ObjectManager\Config',
                    'Magento\Framework\View\LayoutInterface' => 'Magento\TestFramework\View\Layout'
                ]
            );
        }
        return $this->_primaryConfigData;
    }
}
