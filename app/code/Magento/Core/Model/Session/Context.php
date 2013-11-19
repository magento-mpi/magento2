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
     * @var \Magento\Core\Model\Session\Validator
     */
    protected $_validator;

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
     * @var string
     */
    protected $_saveMethod;

    /**
     * @var string
     */
    protected $_savePath;

    /**
     * @var string
     */
    protected $_cacheLimiter;

    /**
     * Core message
     *
     * @var \Magento\Core\Model\Message
     */
    protected $_message;

    /**
     * Core message collection factory
     *
     * @var \Magento\Core\Model\Message\CollectionFactory
     */
    protected $_messageFactory;

    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @var \Magento\App\Dir
     */
    protected $_dir;

    /**
     * @param \Magento\Core\Model\Session\Validator $validator
     * @param \Magento\Logger $logger
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\Message\CollectionFactory $messageFactory
     * @param \Magento\Core\Model\Message $message
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\App\State $appState
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\App\Dir $dir
     * @param $saveMethod
     * @param null $savePath
     * @param null $cacheLimiter
     */
    public function __construct(
        \Magento\Core\Model\Session\Validator $validator,
        \Magento\Logger $logger,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\Message\CollectionFactory $messageFactory,
        \Magento\Core\Model\Message $message,
        \Magento\App\RequestInterface $request,
        \Magento\App\State $appState,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\App\Dir $dir,
        $saveMethod,
        $savePath = null,
        $cacheLimiter = null
    ) {
        $this->_validator = $validator;
        $this->_logger = $logger;
        $this->_eventManager = $eventManager;
        $this->_storeConfig = $coreStoreConfig;
        $this->_saveMethod = $saveMethod;
        $this->_savePath = $savePath;
        $this->_cacheLimiter = $cacheLimiter;
        $this->_messageFactory = $messageFactory;
        $this->_message = $message;
        $this->_request = $request;
        $this->_appState = $appState;
        $this->_storeManager = $storeManager;
        $this->_dir = $dir;
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
     * @return \Magento\Core\Model\Session\Validator
     */
    public function getValidator()
    {
        return $this->_validator;
    }

    /**
     * @return string
     */
    public function getCacheLimiter()
    {
        return $this->_cacheLimiter;
    }

    /**
     * @return string
     */
    public function getSaveMethod()
    {
        return $this->_saveMethod;
    }

    /**
     * @return string
     */
    public function getSavePath()
    {
        return $this->_savePath;
    }

    /**
     * @return \Magento\App\State
     */
    public function getAppState()
    {
        return $this->_appState;
    }

    /**
     * @return \Magento\App\Dir
     */
    public function getDir()
    {
        return $this->_dir;
    }

    /**
     * @return \Magento\Core\Model\Message
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * @return \Magento\Core\Model\Message\CollectionFactory
     */
    public function getMessageFactory()
    {
        return $this->_messageFactory;
    }

    /**
     * @return \Magento\App\RequestInterface
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @return \Magento\Core\Model\StoreManager
     */
    public function getStoreManager()
    {
        return $this->_storeManager;
    }
}
