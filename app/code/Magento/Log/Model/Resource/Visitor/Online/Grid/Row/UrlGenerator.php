<?php
/**
 * URL Generator for Customer Online Grid
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Log\Model\Resource\Visitor\Online\Grid\Row;

class UrlGenerator
    extends \Magento\Backend\Model\Widget\Grid\Row\UrlGenerator
{
    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\AuthorizationInterface $authorization
     * @param array $args
     */
    public function __construct(
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\AuthorizationInterface $authorization,
        array $args = array()
    ) {
        $this->_authorization = $authorization;
        parent::__construct($backendUrl, $args);
    }

    /**
     * Create url for passed item using passed url model
     * @param \Magento\Object $item
     * @return string
     */
    public function getUrl($item)
    {
        if ($this->_authorization->isAllowed('Magento_Customer::manage') && $item->getCustomerId()) {
            return parent::getUrl($item);
        }
        return false;
    }
}
