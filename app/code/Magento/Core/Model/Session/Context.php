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
     * @var \Magento\Core\Model\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\Core\Model\Event\Manager
     */
    protected $_eventManager;

    /**
     * @var \Magento\Core\Helper\Http
     */
    protected $_httpHelper;

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
     * Mapping between area and SID param name
     *
     * @var array
     */
    protected $sidMap;

    /**
     * @param \Magento\Core\Model\Session\Validator $validator
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Helper\Http $coreHttp
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param $saveMethod
     * @param null $savePath
     * @param null $cacheLimiter
     * @param array $sidMap
     */
    public function __construct(
        \Magento\Core\Model\Session\Validator $validator,
        \Magento\Core\Model\Logger $logger,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Helper\Http $coreHttp,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        $saveMethod,
        $savePath = null,
        $cacheLimiter = null,
        $sidMap = array()
    ) {
        $this->_validator = $validator;
        $this->_logger = $logger;
        $this->_eventManager = $eventManager;
        $this->_httpHelper = $coreHttp;
        $this->_storeConfig = $coreStoreConfig;
        $this->_saveMethod = $saveMethod;
        $this->_savePath = $savePath;
        $this->_cacheLimiter = $cacheLimiter;
        $this->sidMap = $sidMap;
    }

    /**
     * @return \Magento\Core\Model\Event\Manager
     */
    public function getEventManager()
    {
        return $this->_eventManager;
    }

    /**
     * @return \Magento\Core\Helper\Http
     */
    public function getHttpHelper()
    {
        return $this->_httpHelper;
    }

    /**
     * @return \Magento\Core\Model\Logger
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
     * @return array
     */
    public function getSidMap()
    {
        return $this->sidMap;
    }
}
