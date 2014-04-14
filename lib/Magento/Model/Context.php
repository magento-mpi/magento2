<?php
/**
 * Abstract model context
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Model;

class Context implements \Magento\ObjectManager\ContextInterface
{
    /**
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventDispatcher;

    /**
     * @var \Magento\App\CacheInterface
     */
    protected $_cacheManager;

    /**
     * @var \Magento\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\Model\ActionValidator\RemoveAction
     */
    protected $_actionValidator;

    /**
     * @param \Magento\Logger $logger
     * @param \Magento\Event\ManagerInterface $eventDispatcher
     * @param \Magento\App\CacheInterface $cacheManager
     * @param \Magento\App\State $appState
     * @param ActionValidator\RemoveAction $actionValidator
     */
    public function __construct(
        \Magento\Logger $logger,
        \Magento\Event\ManagerInterface $eventDispatcher,
        \Magento\App\CacheInterface $cacheManager,
        \Magento\App\State $appState,
        \Magento\Model\ActionValidator\RemoveAction $actionValidator
    ) {
        $this->_eventDispatcher = $eventDispatcher;
        $this->_cacheManager = $cacheManager;
        $this->_appState = $appState;
        $this->_logger = $logger;
        $this->_actionValidator = $actionValidator;
    }

    /**
     * @return \Magento\App\CacheInterface
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
     * @return \Magento\App\State
     */
    public function getAppState()
    {
        return $this->_appState;
    }

    /**
     * @return \Magento\Model\ActionValidator\RemoveAction
     */
    public function getActionValidator()
    {
        return $this->_actionValidator;
    }
}
