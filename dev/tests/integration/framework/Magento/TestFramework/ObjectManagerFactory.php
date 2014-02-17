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

        $objectManager->configure($this->_primaryConfigData);
        $objectManager->addSharedInstance($directoryList, 'Magento\App\Filesystem\DirectoryList');
        $objectManager->addSharedInstance($directoryList, 'Magento\Filesystem\DirectoryList');
        $objectManager->configure(array(
            'Magento\View\Design\FileResolution\Strategy\Fallback\CachingProxy' => array(
                'parameters' => array('canSaveMap' => false)
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
                'Magento\Interception\PluginList\PluginList' => 'Magento\TestFramework\Interception\PluginList'
            ),
        ));

        $options = new \Magento\App\Arguments(
            $arguments,
            new \Magento\App\Arguments\Loader($directoryList)
        );

        $objectManager->addSharedInstance($options, 'Magento\App\Arguments');
        $objectManager->getFactory()->setArguments($options->get());

        $objectManager->get('Magento\Interception\PluginList')->reset();
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
     * Override method in while running integration tests to prevent getting Exception
     *
     * @param \Magento\ObjectManager $objectManager
     */
    protected function configureDirectories(\Magento\ObjectManager $objectManager)
    {
    }
}
