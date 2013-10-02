<?php
/**
 * Abstract model context
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Model;

class Context implements \Magento\ObjectManager\ContextInterface
{
    /**
     * @var \Magento\Core\Model\Event\Manager
     */
    protected $_eventDispatcher;

    /**
     * @var \Magento\Core\Model\CacheInterface
     */
    protected $_cacheManager;

    /**
     * @var \Magento\Core\Model\Logger
     */
    protected $_logger;

    /**
     * @param \Magento\Core\Model\Logger $logger
     * @var \Magento\Core\Model\App\State
     */
    protected $_appState;

    /**
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Core\Model\Event\Manager $eventDispatcher
     * @param \Magento\Core\Model\CacheInterface $cacheManager
     * @param \Magento\Core\Model\App\State $appState
     */
    public function __construct(
        \Magento\Core\Model\Logger $logger,
        \Magento\Core\Model\Event\Manager $eventDispatcher,
        \Magento\Core\Model\CacheInterface $cacheManager,
        \Magento\Core\Model\App\State $appState
    ) {
        $this->_eventDispatcher = $eventDispatcher;
        $this->_cacheManager = $cacheManager;
        $this->_appState = $appState;
        $this->_logger = $logger;
    }

    /**
     * @return \Magento\Core\Model\CacheInterface
     */
    public function getCacheManager()
    {
        return $this->_cacheManager;
    }

    /**
     * @return \Magento\Core\Model\Event\Manager
     */
    public function getEventDispatcher()
    {
        return $this->_eventDispatcher;
    }

    /**
     * @return \Magento\Core\Model\Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }

    /**
     * @return \Magento\Core\Model\App\State
     */
    public function getAppState()
    {
        return $this->_appState;
    }

    /**
     * @return \Magento\Core\Model\StoreManager
     */
    public function getStoreManager()
    {
        return $this->_storeManager;
    }
}
