<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogInventory\Model\Plugin;

class Layer
{
    /**
     * Stock status instance
     *
     * @var \Magento\CatalogInventory\Model\Stock\Status
     */
    protected $_stockStatus;

    /**
     * Store config instance
     *
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_storeConfig;

    /**
     * @param \Magento\CatalogInventory\Model\Stock\Status $stockStatus
     * @param \Magento\App\Config\ScopeConfigInterface $storeConfig
     */
    public function __construct(
        \Magento\CatalogInventory\Model\Stock\Status $stockStatus,
        \Magento\App\Config\ScopeConfigInterface $storeConfig
    ) {
        $this->_stockStatus = $stockStatus;
        $this->_storeConfig = $storeConfig;
    }

    /**
     * Before prepare product collection handler
     *
     * @param \Magento\Catalog\Model\Layer $subject
     * @param \Magento\Catalog\Model\Resource\Collection\AbstractCollection $collection
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforePrepareProductCollection(
        \Magento\Catalog\Model\Layer $subject,
        \Magento\Catalog\Model\Resource\Collection\AbstractCollection $collection
    ) {
        if ($this->_isEnabledShowOutOfStock()) {
            return;
        }
        $this->_stockStatus->addIsInStockFilterToCollection($collection);
    }

    /**
     * Get config value for 'display out of stock' option
     *
     * @return bool
     */
    protected function _isEnabledShowOutOfStock()
    {
        return $this->_storeConfig->isSetFlag('cataloginventory/options/show_out_of_stock', \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE);
    }
}
