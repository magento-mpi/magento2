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

class Request extends \Zend_Controller_Request_Http implements \Magento\App\RequestInterface
{
    /** @var int */
    protected $_consumerId = 0;

    /**
     * Modify pathInfo: strip down the front name and query parameters.
     *
     * @param \Magento\App\AreaList $areaList
     * @param \Magento\Config\ScopeInterface $configScope
     * @param null|string|\Zend_Uri $uri
     */
    public function __construct(
        \Magento\App\AreaList $areaList,
        \Magento\Config\ScopeInterface $configScope,
        $uri = null
    ) {
        parent::__construct($uri);
        $areaFrontName = $areaList->getFrontName($configScope->getCurrentScope());
        $this->_pathInfo = $this->_requestUri;
        /** Remove base url and area from path */
        $this->_pathInfo = preg_replace("#.*?/{$areaFrontName}/?#", '/', $this->_pathInfo);
        /** Remove GET parameters from path */
        $this->_pathInfo = preg_replace('#\?.*#', '', $this->_pathInfo);
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
}
