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
     * @var \Magento\Core\Model\Store\ConfigInterface
     */
    protected $_storeConfig;

    /**
     * @param \Magento\CatalogInventory\Model\Stock\Status $stockStatus
     * @param \Magento\Core\Model\Store\ConfigInterface $storeConfig
     */
    public function __construct(
        \Magento\CatalogInventory\Model\Stock\Status $stockStatus,
        \Magento\Core\Model\Store\ConfigInterface $storeConfig
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
        return $this->_storeConfig->getConfigFlag('cataloginventory/options/show_out_of_stock');
    }
}
