<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reports Product Index Abstract Product Resource Collection
 *
 * @category    Magento
 * @package     Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Model\Resource\Product\Index\Collection;

abstract class AbstractCollection
    extends \Magento\Catalog\Model\Resource\Product\Collection
{
    /**
     * Customer id
     *
     * @var null|int
     */
    protected $_customerId = null;

    /**
     * Retrieve Product Index table name
     *
     */
    abstract protected function _getTableName();

    /**
     * Join index table
     *
     * @return \Magento\Reports\Model\Resource\Product\Index\Collection\AbstractCollection
     */
    protected function _joinIdxTable()
    {
        if (!$this->getFlag('is_idx_table_joined')) {
            $this->joinTable(
                array('idx_table' => $this->_getTableName()),
                'product_id=entity_id',
                array(
                    'product_id'    => 'product_id',
                    'item_store_id' => 'store_id',
                    'added_at'      => 'added_at'
                ),
                $this->_getWhereCondition()
            );
            $this->setFlag('is_idx_table_joined', true);
        }
        return $this;
    }

    /**
     * Add Viewed Products Index to Collection
     *
     * @return \Magento\Reports\Model\Resource\Product\Index\Collection\AbstractCollection
     */
    public function addIndexFilter()
    {
        $this->_joinIdxTable();
        $this->_productLimitationFilters['store_table'] = 'idx_table';
        $this->setFlag('url_data_object', true);
        $this->setFlag('do_not_use_category_id', true);
        return $this;
    }

    /**
     * Add filter by product ids
     *
     * @param array $ids
     * @return \Magento\Reports\Model\Resource\Product\Index\Collection\AbstractCollection
     */
    public function addFilterByIds($ids)
    {
        if (empty($ids)) {
            $this->getSelect()->where('1=0');
        } else {
            $this->getSelect()->where('e.entity_id IN(?)', $ids);
        }
        return $this;
    }

    /**
     * Retrieve Where Condition to Index table
     *
     * @return array
     */
    protected function _getWhereCondition()
    {
        $condition = array();

        if (\Mage::getSingleton('Magento\Customer\Model\Session')->isLoggedIn()) {
            $condition['customer_id'] = \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerId();
        } elseif ($this->_customerId) {
            $condition['customer_id'] = $this->_customerId;
        } else {
            $condition['visitor_id'] = \Mage::getSingleton('Magento\Log\Model\Visitor')->getId();
        }

        return $condition;
    }

    /**
     * Set customer id, that will be used in 'whereCondition'
     *
     * @param int $id
     * @return \Magento\Reports\Model\Resource\Product\Index\Collection\AbstractCollection
     */
    public function setCustomerId($id)
    {
        $this->_customerId = (int)$id;
        return $this;
    }

    /**
     * Add order by "added at"
     *
     * @param string $dir
     * @return \Magento\Reports\Model\Resource\Product\Index\Collection\AbstractCollection
     */
    public function setAddedAtOrder($dir = self::SORT_ORDER_DESC)
    {
        if ($this->getFlag('is_idx_table_joined')) {
            $this->getSelect()->order('added_at ' . $dir);
        }
        return $this;
    }

    /**
     * Add exclude Product Ids
     *
     * @param int|array $productIds
     * @return \Magento\Reports\Model\Resource\Product\Index\Collection\AbstractCollection
     */
    public function excludeProductIds($productIds)
    {
        if (empty($productIds)) {
            return $this;
        }
        $this->_joinIdxTable();
        $this->getSelect()->where('idx_table.product_id NOT IN(?)', $productIds);
        return $this;
    }
}
