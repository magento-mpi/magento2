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

class ScopeDefiner
{
    const SCOPE_WEBSITE = 'website';
    const SCOPE_STORE = 'store';
    const SCOPE_DEFAULT = 'default';

    /**
     * Request object
     *
     * @var \Magento\Core\Controller\Request\Http
     */
    protected $_request;

    /**
     * @param \Magento\Core\Controller\Request\Http $request
     */
    public function __construct(\Magento\Core\Controller\Request\Http $request)
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
            ? self::SCOPE_STORE
            : ($this->_request->getParam('website') ? self::SCOPE_WEBSITE : self::SCOPE_DEFAULT);
    }
}
