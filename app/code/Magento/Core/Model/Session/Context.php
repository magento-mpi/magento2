<?php
/**
 * Core Session Context Model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Session;

class Context implements \Magento\ObjectManager\ContextInterface
{
    /**
     * @var \Magento\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_storeConfig;

    /**
     * Core message
     *
     * @var \Magento\Message\Factory
     */
    protected $messageFactory;

    /**
     * Core message collection factory
     *
     * @var \Magento\Message\CollectionFactory
     */
    protected $messagesFactory;

    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Logger $logger
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Message\CollectionFactory $messagesFactory
     * @param \Magento\Message\Factory $messageFactory
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Logger $logger,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Message\CollectionFactory $messagesFactory,
        \Magento\Message\Factory $messageFactory,
        \Magento\App\RequestInterface $request,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->_logger = $logger;
        $this->_eventManager = $eventManager;
        $this->_storeConfig = $coreStoreConfig;
        $this->messagesFactory = $messagesFactory;
        $this->messageFactory = $messageFactory;
        $this->_request = $request;
        $this->_storeManager = $storeManager;
    }

    /**
     * @return \Magento\Event\ManagerInterface
     */
    public function getEventManager()
    {
        return $this->_eventManager;
    }

    /**
     * @return \\Magento\Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }

    /**
     * @return \Magento\Core\Model\Store\Config
     */
    public function getStoreConfig()
    {
        return $this->_storeConfig;
    }

    /**
     * @return \Magento\Message\Factory
     */
    public function getMessageFactory()
    {
        return $this->messageFactory;
    }

    /**
     * @return \Magento\Message\CollectionFactory
     */
    public function getMessagesFactory()
    {
        return $this->messagesFactory;
    }

    /**
     * @return \Magento\App\RequestInterface
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @return \Magento\Core\Model\StoreManagerInterface
     */
    public function getStoreManager()
    {
        return $this->_storeManager;
    }
}
