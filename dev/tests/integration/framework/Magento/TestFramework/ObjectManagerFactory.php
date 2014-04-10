<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework;

/**
 * Class ObjectManagerFactory
 *
 * @package Magento\TestFramework
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
     * @throws \Magento\Exception
     */
    protected function createAppArguments(
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        array $arguments
    ) {
        if ($this->appArgumentsProxy) {
            // Framework constraint: this is ambiguous situation, because it is not clear what to do with older instance
            throw new \Magento\Exception('Only one creation of application arguments is supported');
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
        $directories = isset(
            $arguments[\Magento\Framework\App\Filesystem::PARAM_APP_DIRS]
        ) ? $arguments[\Magento\Framework\App\Filesystem::PARAM_APP_DIRS] : array();
        $directoryList = new \Magento\TestFramework\App\Filesystem\DirectoryList($rootDir, $directories);

        \Magento\TestFramework\ObjectManager::setInstance($objectManager);

        $objectManager->configure($this->_primaryConfigData);
        $objectManager->addSharedInstance($directoryList, 'Magento\Framework\App\Filesystem\DirectoryList');
        $objectManager->addSharedInstance($directoryList, 'Magento\Filesystem\DirectoryList');

        $appArguments = parent::createAppArguments($directoryList, $arguments);
        $this->appArgumentsProxy->setSubject($appArguments);
        $this->factory->setArguments($appArguments->get());
        $objectManager->addSharedInstance($appArguments, 'Magento\Framework\App\Arguments');

        $objectManager->get('Magento\Interception\PluginList')->reset();
        $objectManager->configure($objectManager->get('Magento\Framework\App\ObjectManager\ConfigLoader')
            ->load('global'));

        return $objectManager;
    }

    /**
     * Load primary config
     *
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param mixed $argumentMapper
     * @param string $appMode
     * @return array
     */
    protected function _loadPrimaryConfig(
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        $argumentMapper,
        $appMode
    ) {
        if (null === $this->_primaryConfigData) {
            $this->_primaryConfigData = array_replace(
                parent::_loadPrimaryConfig($directoryList, $argumentMapper, $appMode),
                array(
                    'Magento\View\Design\FileResolution\Strategy\Fallback\CachingProxy' => array(
                        'arguments' => array(
                            'canSaveMap' => false
                        )
                    ),
                    'default_setup' => array('type' => 'Magento\TestFramework\Db\ConnectionAdapter')
                )
            );
            $this->_primaryConfigData['preferences'] = array_replace(
                $this->_primaryConfigData['preferences'],
                [
                    'Magento\Stdlib\Cookie' => 'Magento\TestFramework\Cookie',
                    'Magento\Framework\App\RequestInterface' => 'Magento\TestFramework\Request',
                    'Magento\Framework\App\Request\Http' => 'Magento\TestFramework\Request',
                    'Magento\Framework\App\ResponseInterface' => 'Magento\TestFramework\Response',
                    'Magento\Framework\App\Response\Http' => 'Magento\TestFramework\Response',
                    'Magento\Interception\PluginList' => 'Magento\TestFramework\Interception\PluginList',
                    'Magento\Interception\ObjectManager\Config' => 'Magento\TestFramework\ObjectManager\Config',
                    'Magento\View\LayoutInterface' => 'Magento\TestFramework\View\Layout'
                ]
            );
        }
        return $this->_primaryConfigData;
    }

    /**
     * Override method in while running integration tests to prevent getting Exception
     *
     * @param \Magento\ObjectManager $objectManager
     */
    protected function configureDirectories(\Magento\ObjectManager $objectManager)
    {
    }
}
