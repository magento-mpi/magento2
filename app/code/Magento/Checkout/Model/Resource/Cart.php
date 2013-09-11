<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Resource model for Checkout Cart
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Model\Resource;

class Cart extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('sales_flat_quote', 'entity_id');
    }

    /**
     * Fetch items summary
     *
     * @param int $quoteId
     * @return array
     */
    public function fetchItemsSummary($quoteId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from(array('q'=>$this->getTable('sales_flat_quote')), array('items_qty', 'items_count'))
            ->where('q.entity_id = :quote_id');

        $result = $read->fetchRow($select, array(':quote_id' => $quoteId));
        return $result ? $result : array('items_qty'=>0, 'items_count'=>0);
    }

    /**
     * Fetch items
     *
     * @param int $quoteId
     * @return array
     */
    public function fetchItems($quoteId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from(array('qi'=>$this->getTable('sales_flat_quote_item')),
                array('id'=>'item_id', 'product_id', 'super_product_id', 'qty', 'created_at'))
            ->where('qi.quote_id = :quote_id');

        return $read->fetchAll($select, array(':quote_id' => $quoteId));
    }

    /**
     * Make collection not to load products that are in specified quote
     *
     * @param \Magento\Catalog\Model\Resource\Product\Collection $collection
     * @param int $quoteId
     * @return \Magento\Checkout\Model\Resource\Cart
     */
    public function addExcludeProductFilter($collection, $quoteId)
    {
        $adapter = $this->_getReadAdapter();
        $exclusionSelect = $adapter->select()
            ->from($this->getTable('sales_flat_quote_item'), array('product_id'))
            ->where('quote_id = ?', $quoteId);
        $condition = $adapter->prepareSqlCondition('e.entity_id', array('nin' => $exclusionSelect));
        $collection->getSelect()->where($condition);
        return $this;
    }
}
