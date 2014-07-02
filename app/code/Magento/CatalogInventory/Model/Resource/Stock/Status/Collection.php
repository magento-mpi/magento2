<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogInventory\Model\Resource\Stock\Status;

/**
 * Stock status collection resource model
 */
class Collection extends \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Magento\CatalogInventory\Model\Stock\Status',
            'Magento\CatalogInventory\Model\Resource\Stock\Status'
        );
    }

    /**
     * Filter status by website
     *
     * @param \Magento\Store\Model\Website $website
     * @return $this
     */
    public function addWebsiteFilter(\Magento\Store\Model\Website $website)
    {
        $this->addFieldToFilter('website_id', $website->getWebsiteId());
        return $this;
    }

    /**
     * Add filter by quantity to collection
     *
     * @param float $qty
     * @return $this
     */
    public function addQtyFilter($qty)
    {
        return $this->addFieldToFilter('main_table.qty', ['lteq' => $qty]);
    }

    /**
     * Initialize select object
     *
     * @return $this
     */
    protected function _initSelect()
    {
        return parent::_initSelect()->getSelect()->join(
            array('cp_table' => $this->getTable('catalog_product_entity')),
            'main_table.product_id = cp_table.entity_id',
            array('sku', 'type_id')
        );
    }
}
