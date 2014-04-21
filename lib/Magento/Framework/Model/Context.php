<?php
/**
 * Abstract model context
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Framework\Model;

class Context implements \Magento\ObjectManager\ContextInterface
{
    /**
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventDispatcher;

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $_cacheManager;

    /**
     * @var \Magento\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\Framework\Model\ActionValidator\RemoveAction
     */
    protected $_actionValidator;

    /**
     * @param \Magento\Logger $logger
     * @param \Magento\Event\ManagerInterface $eventDispatcher
     * @param \Magento\Framework\App\CacheInterface $cacheManager
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\Model\ActionValidator\RemoveAction $actionValidator
     */
    public function __construct(
        \Magento\Logger $logger,
        \Magento\Event\ManagerInterface $eventDispatcher,
        \Magento\Framework\App\CacheInterface $cacheManager,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\Model\ActionValidator\RemoveAction $actionValidator
    ) {
        $this->_eventDispatcher = $eventDispatcher;
        $this->_cacheManager = $cacheManager;
        $this->_appState = $appState;
        $this->_logger = $logger;
        $this->_actionValidator = $actionValidator;
    }

    /**
     * @return \Magento\Framework\App\CacheInterface
     */
    public function getCacheManager()
    {
        return $this->_cacheManager;
    }

    /**
     * @return \Magento\Event\ManagerInterface
     */
    public function getEventDispatcher()
    {
        return $this->_eventDispatcher;
    }

    /**
     * @return \Magento\Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }

    /**
     * @return \Magento\Framework\App\State
     */
    public function getAppState()
    {
        return $this->_appState;
    }

    /**
     * @return \Magento\Framework\Model\ActionValidator\RemoveAction
     */
    public function getActionValidator()
    {
        return $this->_actionValidator;
    }
}
