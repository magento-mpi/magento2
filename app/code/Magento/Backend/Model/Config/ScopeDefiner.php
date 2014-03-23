<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System configuration scope
 */
namespace Magento\Backend\Model\Config;

class ScopeDefiner implements \Magento\BaseScopeResolverInterface
{
    /**
     * Request object
     *
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * @param \Magento\App\RequestInterface $request
     */
    public function __construct(\Magento\App\RequestInterface $request)
    {
        $this->_request = $request;
    }

    /**
     * Retrieve current config scope
     *
     * @return string
     */
    public function getScope()
    {
        return $this->_request->getParam('store')
            ? \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            : ($this->_request->getParam('website') ? \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE : \Magento\BaseScopeInterface::SCOPE_DEFAULT);
    }
}
