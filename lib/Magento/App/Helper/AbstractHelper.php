<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Helper;

/**
 * Abstract helper
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class AbstractHelper
{
    /**
     * Helper module name
     *
     * @var string
     */
    protected $_moduleName;

    /**
     * Request object
     *
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * Translator model
     *
     * @var \Magento\TranslateInterface
     */
    protected $_inlineFactory;

    /**
     * @var \Magento\Module\Manager
     */
    private $_moduleManager;

    /**
     * @var \Magento\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\Core\Model\App
     */
    protected $_app;

    /**
     * @var \Magento\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\HTTP\Header
     */
    protected $_httpHeader;

    /**
     * Event manager
     *
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $_remoteAddress;

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->_inlineFactory = $context->getInlineFactory();
        $this->_moduleManager = $context->getModuleManager();
        $this->_logger = $context->getLogger();
        $this->_request = $context->getRequest();
        $this->_app = $context->getApp();
        $this->_urlBuilder = $context->getUrlBuilder();
        $this->_httpHeader = $context->getHttpHeader();
        $this->_eventManager = $context->getEventManager();
        $this->_remoteAddress = $context->getRemoteAddress();
        $this->_cacheConfig = $context->getCacheConfig();
    }

    /**
     * Retrieve request object
     *
     * @return \Magento\App\RequestInterface
     */
    protected function _getRequest()
    {
        return $this->_request;
    }

    /**
     * Retrieve helper module name
     *
     * @return string
     */
    protected function _getModuleName()
    {
        if (!$this->_moduleName) {
            $class = get_class($this);
            $this->_moduleName = substr($class, 0, strpos($class, '\\Helper'));
        }
        return str_replace(\Magento\Autoload\IncludePath::NS_SEPARATOR, '_', $this->_moduleName);
    }

    /**
     * Check whether or not the module output is enabled in Configuration
     *
     * @param string $moduleName Full module name
     * @return boolean
     * @deprecated use \Magento\Module\Manager::isOutputEnabled()
     */
    public function isModuleOutputEnabled($moduleName = null)
    {
        if ($moduleName === null) {
            $moduleName = $this->_getModuleName();
        }
        return $this->_moduleManager->isOutputEnabled($moduleName);
    }

    /**
     * Check is module exists and enabled in global config.
     *
     * @param string $moduleName the full module name, example Magento_Core
     * @return boolean
     * @deprecated use \Magento\Module\Manager::isEnabled()
     */
    public function isModuleEnabled($moduleName = null)
    {
        if ($moduleName === null) {
            $moduleName = $this->_getModuleName();
        }
        return $this->_moduleManager->isEnabled($moduleName);
    }

    /**
     * Retrieve url
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    protected function _getUrl($route, $params = array())
    {
        return $this->_urlBuilder->getUrl($route, $params);
    }

    /**
     * base64_encode() for URLs encoding
     *
     * @param    string $url
     * @return   string
     */
    public function urlEncode($url)
    {
        return strtr(base64_encode($url), '+/=', '-_,');
    }

    /**
     *  base64_decode() for URLs decoding
     *
     * @param    string $url
     * @return   string
     */
    public function urlDecode($url)
    {
        $url = base64_decode(strtr($url, '-_,', '+/='));
        return $this->_urlBuilder->sessionUrlVar($url);
    }

    /**
     *   Translate array
     *
     * @param    array $arr
     * @return   array
     */
    public function translateArray($arr = array())
    {
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $v = self::translateArray($v);
            } elseif ($k === 'label') {
                $v = __($v);
            }
            $arr[$k] = $v;
        }
        return $arr;
    }
}
