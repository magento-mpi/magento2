<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogPermissions\Model\View\Action\LayoutService\Plugin;

class CatalogPermissions
{
    /**
     * Catalog permission helper
     *
     * @var \Magento\CatalogPermissions\Helper\Data
     */
    protected $_catalogPermData;

    /**
     * @var \Magento\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @param \Magento\CatalogPermissions\Helper\Data $catalogPermData
     * @param \Magento\View\LayoutInterface $layout
     */
    public function __construct(
        \Magento\CatalogPermissions\Helper\Data $catalogPermData,
        \Magento\View\LayoutInterface $layout
    ) {
        $this->_layout = $layout;
        $this->_catalogPermData = $catalogPermData;
    }

    /**
     * Check catalog search availability on load layout
     */
    public function aroundLoadLayoutUpdates(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        if ($this->_catalogPermData->isEnabled() && !$this->_catalogPermData->isAllowedCatalogSearch()) {
            $this->_layout->getUpdate()->addHandle('CATALOGPERMISSIONS_DISABLED_CATALOG_SEARCH');
        }
        return $invocationChain->proceed($arguments);
    }
} 