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
 * Report Customers Tags collection
 *
 * @category    Magento
 * @package     Magento_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Model_Resource_Reports_Customer_Collection extends Magento_Tag_Model_Resource_Customer_Collection
{
    /**
     * Add target count
     *
     * @return Magento_Tag_Model_Resource_Reports_Customer_Collection
     */
    public function addTagedCount()
    {
        $this->getSelect()
            ->columns(array('taged' => 'COUNT(tr.tag_relation_id)'));
        return $this;
    }

    /**
     * get select count sql
     *
     * @return string
     */
    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->columns("COUNT(DISTINCT tr.customer_id)");

        return $countSelect;
    }
}
