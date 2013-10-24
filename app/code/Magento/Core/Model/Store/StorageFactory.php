<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Model\Store;

class StorageFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Default storage class name
     *
     * @var string
     */
    protected $_defaultStorageClassName;

    /**
     * Installed storage class name
     *
     * @var string
     */
    protected $_installedStoreClassName;

    /**
     * @var \Magento\Core\Model\Store\StorageInterface[]
     */
    protected $_cache = array();

    /**
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Logger
     */
    protected $_log;

    /**
     * @var \Magento\Core\Model\ConfigInterface
     */
    protected $_config;

    /**
     * @var \Magento\Core\Model\App\Proxy
     */
    protected $_app;

    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Logger $logger
     * @param \Magento\Core\Model\ConfigInterface $config
     * @param \Magento\Core\Model\App\Proxy $app
     * @param \Magento\App\State $appState
     * @param string $defaultStorageClassName
     * @param string $installedStoreClassName
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Logger $logger,
        \Magento\Core\Model\ConfigInterface $config,
        \Magento\Core\Model\App\Proxy $app,
        \Magento\App\State $appState,
        $defaultStorageClassName = 'Magento\Core\Model\Store\Storage\DefaultStorage',
        $installedStoreClassName = 'Magento\Core\Model\Store\Storage\Db'
    ) {
        $this->_objectManager = $objectManager;
        $this->_defaultStorageClassName = $defaultStorageClassName;
        $this->_installedStoreClassName = $installedStoreClassName;
        $this->_eventManager = $eventManager;
        $this->_log = $logger;
        $this->_appState = $appState;
        $this->_config = $config;
        $this->_app = $app;
    }

    /**
     * Get storage instance
     *
     * @param array $arguments
     * @return \Magento\Core\Model\Store\StorageInterface
     * @throws \InvalidArgumentException
     */
    public function get(array $arguments = array())
    {
        $className = $this->_appState->isInstalled() ?
            $this->_installedStoreClassName :
            $this->_defaultStorageClassName;

        if (false == isset($this->_cache[$className])) {
            /** @var $instance \Magento\Core\Model\Store\StorageInterface */
            $instance = $this->_objectManager->create($className, $arguments);

            if (false === ($instance instanceof \Magento\Core\Model\Store\StorageInterface)) {
                throw new \InvalidArgumentException($className
                        . ' doesn\'t implement \Magento\Core\Model\Store\StorageInterface'
                );
            }
            $this->_cache[$className] = $instance;
            $instance->initCurrentStore();
            if ($className === $this->_installedStoreClassName) {
                $useSid = $instance->getStore()
                    ->getConfig(\Magento\Core\Model\Session\AbstractSession::XML_PATH_USE_FRONTEND_SID);
                $this->_app->setUseSessionInUrl($useSid);

                $this->_eventManager->dispatch('core_app_init_current_store_after');

                $store = $instance->getStore(true);
                if ($store->getConfig('dev/log/active')) {
                    $writer = (string)$this->_config->getNode('global/log/core/writer_model');

                    $this->_log->unsetLoggers();
                    $this->_log->addStreamLog(
                        \Magento\Logger::LOGGER_SYSTEM, $store->getConfig('dev/log/file'), $writer);
                    $this->_log->addStreamLog(
                        \Magento\Logger::LOGGER_EXCEPTION, $store->getConfig('dev/log/exception_file'), $writer);
                }
            }
        }
        return $this->_cache[$className];
    }
}
