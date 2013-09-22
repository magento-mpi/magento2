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
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * @param Magento_Core_Model_Logger $logger
     * @param \Magento\Core\Model\Event\Manager $eventDispatcher
     * @param \Magento\Core\Model\CacheInterface $cacheManager
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        \Magento\Core\Model\Event\Manager $eventDispatcher,
        \Magento\Core\Model\CacheInterface $cacheManager
    ) {
        $this->_eventDispatcher = $eventDispatcher;
        $this->_cacheManager = $cacheManager;
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
     * @return Magento_Core_Model_Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }
}
