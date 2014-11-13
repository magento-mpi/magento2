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
     * Restore locator instance
     *
     * @param ObjectManager $objectManager
     * @param DirectoryList $directoryList
     * @param array $arguments
     * @return ObjectManager
     */
    public function restore(ObjectManager $objectManager, $directoryList, array $arguments)
    {
        \Magento\TestFramework\ObjectManager::setInstance($objectManager);
        $this->directoryList = $directoryList;
        $objectManager->configure($this->_primaryConfigData);
        $objectManager->addSharedInstance($this->directoryList, 'Magento\Framework\App\Filesystem\DirectoryList');
        $objectManager->addSharedInstance($this->directoryList, 'Magento\Framework\Filesystem\DirectoryList');
        $deploymentConfig = $this->createDeploymentConfig($directoryList, $arguments);
        $this->factory->setArguments($deploymentConfig->get());

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
                    'Magento\Framework\View\LayoutInterface' => 'Magento\TestFramework\View\Layout',
                    'Magento\Framework\App\Resource\ConnectionFactory' =>
                        'Magento\TestFramework\Db\ConnectionFactory',
                ]
            );
        }
        return $this->_primaryConfigData;
    }
}
