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
class ObjectManagerFactory extends \Magento\App\ObjectManagerFactory
{
    /**
     * Locator class name
     *
     * @var string
     */
    protected $_locatorClassName = '\Magento\TestFramework\ObjectManager';

    /**
     * Config class name
     *
     * @var string
     */
    protected $_configClassName = '\Magento\TestFramework\ObjectManager\Config';

    /**
     * @var array
     */
    protected $_primaryConfigData = null;

    /**
     * @var \Magento\TestFramework\Interception\PluginList
     */
    protected $_pluginList = null;

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
     * @param \Magento\App\Filesystem\DirectoryList $directoryList
     * @param array $arguments
     * @return App\Arguments\Proxy
     */
    protected function createAppArguments(\Magento\App\Filesystem\DirectoryList $directoryList, array $arguments)
    {
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
        $directories = isset($arguments[\Magento\App\Filesystem::PARAM_APP_DIRS])
            ? $arguments[\Magento\App\Filesystem::PARAM_APP_DIRS]
            : array();
        $directoryList = new \Magento\TestFramework\App\Filesystem\DirectoryList($rootDir, $directories);

        \Magento\TestFramework\ObjectManager::setInstance($objectManager);

        $this->_pluginList->reset();

        $objectManager->configure($this->_primaryConfigData);
        $objectManager->addSharedInstance($directoryList, 'Magento\App\Filesystem\DirectoryList');
        $objectManager->addSharedInstance($directoryList, 'Magento\Filesystem\DirectoryList');
        $objectManager->configure(array(
            'Magento\View\Design\FileResolution\Strategy\Fallback\CachingProxy' => array(
                'arguments' => array(
                    'canSaveMap' => array(
                        \Magento\ObjectManager\Config\Reader\Dom::TYPE_ATTRIBUTE => 'boolean',
                        'value' => false
                    ),
                )
            ),
            'default_setup' => array(
                'type' => 'Magento\TestFramework\Db\ConnectionAdapter'
            ),
            'preferences' => array(
                'Magento\Stdlib\Cookie' => 'Magento\TestFramework\Cookie',
                'Magento\App\RequestInterface' => 'Magento\TestFramework\Request',
                'Magento\App\Request\Http' => 'Magento\TestFramework\Request',
                'Magento\App\ResponseInterface' => 'Magento\TestFramework\Response',
                'Magento\App\Response\Http' => 'Magento\TestFramework\Response',
            ),
        ));

        $appArguments = parent::createAppArguments($directoryList, $arguments);
        $this->appArgumentsProxy->setSubject($appArguments);
        $objectManager->addSharedInstance($appArguments, 'Magento\App\Arguments');

        $objectManager->configure(
            $objectManager->get('Magento\App\ObjectManager\ConfigLoader')->load('global')
        );

        return $objectManager;
    }

    /**
     * Load primary config data
     *
     * @param string $configDirectoryPath
     * @param string $appMode
     * @return array
     * @throws \Magento\BootstrapException
     */
    protected function _loadPrimaryConfig($configDirectoryPath, $appMode)
    {
        if (null === $this->_primaryConfigData) {
            $this->_primaryConfigData = parent::_loadPrimaryConfig($configDirectoryPath, $appMode);
        }
        return $this->_primaryConfigData;
    }

    /**
     * Create plugin list object
     *
     * @param \Magento\ObjectManager $locator
     * @param \Magento\ObjectManager\Relations $relations
     * @param \Magento\ObjectManager\DefinitionFactory $definitionFactory
     * @param \Magento\ObjectManager\Config\Config $diConfig
     * @param \Magento\ObjectManager\Definition $definitions
     * @return \Magento\Interception\PluginList\PluginList
     */
    protected function _createPluginList(
        \Magento\ObjectManager $locator,
        \Magento\ObjectManager\Relations $relations,
        \Magento\ObjectManager\DefinitionFactory $definitionFactory,
        \Magento\ObjectManager\Config\Config $diConfig,
        \Magento\ObjectManager\Definition $definitions
    ) {
        $locator->configure(array('preferences' =>
            array('Magento\Interception\PluginList\PluginList' => 'Magento\TestFramework\Interception\PluginList')
        ));
        $this->_pluginList = parent::_createPluginList(
            $locator, $relations, $definitionFactory, $diConfig, $definitions
        );
        return $this->_pluginList;
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
