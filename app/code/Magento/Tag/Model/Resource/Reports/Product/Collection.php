<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Report Products Tags collection
 *
 * @category    Magento
 * @package     Magento_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Model_Resource_Reports_Product_Collection extends Magento_Tag_Model_Resource_Product_Collection
{
    /**
     * Add unique target count to result
     *
     * @return Magento_Tag_Model_Resource_Reports_Product_Collection
     */
    public function addUniqueTagedCount()
    {
        $select = clone $this->getSelect();

        $select->reset()
            ->from(array('rel' => $this->getTable('tag_relation')), 'COUNT(DISTINCT rel.tag_id)')
            ->where('rel.product_id = e.entity_id');

        $this->getSelect()
            ->columns(array('utaged' => new Zend_Db_Expr(sprintf('(%s)', $select))));
        return $this;
    }

    /**
     * Add all target count to result
     *
     * @return Magento_Tag_Model_Resource_Reports_Product_Collection
     */
    public function addAllTagedCount()
    {
        $this->getSelect()
            ->columns(array('taged' => 'COUNT(relation.tag_id)'));
        return $this;
    }

    /**
     * Add target count to result
     *
     * @return Magento_Tag_Model_Resource_Reports_Product_Collection
     */
    public function addTagedCount()
    {
        $this->getSelect()
            ->columns(array('taged' => 'COUNT(relation.tag_relation_id)'));

        return $this;
    }

    /**
     * Add group by product to result
     *
     * @return Magento_Tag_Model_Resource_Reports_Product_Collection
     */
    public function addGroupByProduct()
    {
        $this->getSelect()
            ->group('relation.product_id');
        return $this;
    }

    /**
     * Add group by tag to result
     *
     * @return Magento_Tag_Model_Resource_Reports_Product_Collection
     */
    public function addGroupByTag()
    {
        $this->getSelect()
            ->group('relation.tag_id');
        return $this;
    }

    /**
     * Add product filter
     *
     * @param int $customerId
     * @return Magento_Tag_Model_Resource_Reports_Product_Collection
     */
    public function addProductFilter($customerId)
    {
        $this->getSelect()
             ->where('relation.product_id = ?', (int)$customerId);
        $this->_customerFilterId = (int)$customerId;
        return $this;
    }

    /**
     * Set order
     *
     * @param string $attribute
     * @param string $dir
     * @return Magento_Tag_Model_Resource_Reports_Product_Collection
     */
    public function setOrder($attribute, $dir = self::SORT_ORDER_DESC)
    {
        if ($attribute == 'utaged' || $attribute == 'taged' || $attribute == 'tag_name') {
            $this->getSelect()->order($attribute . ' ' . $dir);
        } else {
            parent::setOrder($attribute, $dir);
        }

        return $this;
    }

    /**
     * Join fields
     *
     * @return Magento_Tag_Model_Resource_Reports_Product_Collection
     */
    protected function _joinFields()
    {
        $this->addAttributeToSelect('name');
        $this->getSelect()
            ->join(
                array('relation' => $this->getTable('tag_relation')),
                'relation.product_id = e.entity_id',
                array())
            ->join(
                array('t' => $this->getTable('tag')),
                't.tag_id = relation.tag_id',
                array('tag_id',  'status', 'tag_name' => 'name')
            );

        return $this;
    }
}
