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
     * @var \Magento\Session\SidResolverInterface
     */
    protected $_sidResolver;

    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @var string
     */
    protected $_writerModel;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Logger $logger
     * @param \Magento\Session\SidResolverInterface $sidResolver
     * @param \Magento\App\State $appState
     * @param string $defaultStorageClassName
     * @param string $installedStoreClassName
     * @param string $writerModel
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Logger $logger,
        \Magento\Session\SidResolverInterface $sidResolver,
        \Magento\App\State $appState,
        $defaultStorageClassName = 'Magento\Core\Model\Store\Storage\DefaultStorage',
        $installedStoreClassName = 'Magento\Core\Model\Store\Storage\Db',
        $writerModel = ''
    ) {
        $this->_objectManager = $objectManager;
        $this->_defaultStorageClassName = $defaultStorageClassName;
        $this->_installedStoreClassName = $installedStoreClassName;
        $this->_eventManager = $eventManager;
        $this->_log = $logger;
        $this->_appState = $appState;
        $this->_sidResolver = $sidResolver;
        $this->_writerModel = $writerModel;
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
                    ->getConfig(\Magento\Core\Model\Session\SidResolver::XML_PATH_USE_FRONTEND_SID);
                $this->_sidResolver->setUseSessionInUrl($useSid);

                $this->_eventManager->dispatch('core_app_init_current_store_after');

                $store = $instance->getStore(true);
                if ($store->getConfig('dev/log/active')) {

                    $this->_log->unsetLoggers();
                    $this->_log->addStreamLog(
                        \Magento\Logger::LOGGER_SYSTEM, $store->getConfig('dev/log/file'), $this->_writerModel);
                    $this->_log->addStreamLog(
                        \Magento\Logger::LOGGER_EXCEPTION,
                        $store->getConfig('dev/log/exception_file'),
                        $this->_writerModel
                    );
                }
            }
        }
        return $this->_cache[$className];
    }
}
