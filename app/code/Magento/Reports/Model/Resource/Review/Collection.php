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
 * Report Reviews collection
 *
 * @category    Magento
 * @package     Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reports_Model_Resource_Review_Collection extends Magento_Review_Model_Resource_Review_Collection
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Review_Model_Review', 'Magento_Review_Model_Resource_Review');
    }

    /**
     * add product filter
     *
     * @param unknown_type $productId
     * @return Magento_Reports_Model_Resource_Review_Collection
     */
    public function addProductFilter($productId)
    {
        $this->addFieldToFilter('entity_pk_value', array('eq' => (int)$productId));

        return $this;
    }

    /**
     * Reset select
     *
     * @return Magento_Reports_Model_Resource_Review_Collection
     */
    public function resetSelect()
    {
        parent::resetSelect();
        $this->_joinFields();
        return $this;
    }

    /**
     * Get select count sql
     *
     * @return string
     */
    public function getSelectCountSql()
    {
        $countSelect = clone $this->_select;
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->columns("COUNT(main_table.review_id)");

        return $countSelect;
    }

    /**
     * Set order
     *
     * @param string $attribute
     * @param string $dir
     * @return Magento_Reports_Model_Resource_Review_Collection
     */
    public function setOrder($attribute, $dir = self::SORT_ORDER_DESC)
    {
        if (in_array($attribute, array('nickname', 'title', 'detail', 'created_at'))) {
            $this->_select->order($attribute . ' ' . $dir);
        } else {
            parent::setOrder($attribute, $dir);
        }

        return $this;
    }
}
