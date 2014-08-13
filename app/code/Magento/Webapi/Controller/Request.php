<?php
/**
 * Web API request.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller;

class Request extends \Zend_Controller_Request_Http implements \Magento\Framework\App\RequestInterface
{
    /** @var int */
    protected $_consumerId = 0;

    /**
     * @var \Magento\Framework\Stdlib\CookieManager
     */
    protected $_cookieManager;

    /**
     * Modify pathInfo: strip down the front name and query parameters.
     *
     * @param \Magento\Framework\App\AreaList $areaList
     * @param \Magento\Framework\Config\ScopeInterface $configScope
     * @param \Magento\Framework\Stdlib\CookieManager $cookieManager
     * @param null|string|\Zend_Uri $uri
     */
    public function __construct(
        \Magento\Framework\App\AreaList $areaList,
        \Magento\Framework\Config\ScopeInterface $configScope,
        \Magento\Framework\Stdlib\CookieManager $cookieManager,
        $uri = null
    ) {
        parent::__construct($uri);
        $areaFrontName = $areaList->getFrontName($configScope->getCurrentScope());
        $this->_pathInfo = $this->_requestUri;
        /** Remove base url and area from path */
        $this->_pathInfo = preg_replace("#.*?/{$areaFrontName}/?#", '/', $this->_pathInfo);
        /** Remove GET parameters from path */
        $this->_pathInfo = preg_replace('#\?.*#', '', $this->_pathInfo);
        $this->_cookieManager = $cookieManager;
    }

    /**
     * Set consumer ID.
     *
     * @param int $consumerId
     * @return void
     */
    public function setConsumerId($consumerId)
    {
        $this->_consumerId = $consumerId;
    }

    /**
     * Get consumer ID.
     *
     * @return int
     */
    public function getConsumerId()
    {
        return $this->_consumerId;
    }

    /**
     * Retrieve a value from a cookie.
     *
     * @param string|null $name
     * @param string|null $default The default value to return if no value could be found for the given $name.
     * @return string|null|array
     */
    public function getCookie($name = null, $default = null)
    {
        return isset($name) ? $this->_cookieManager->getCookie($name, $default) : [];
    }
}
