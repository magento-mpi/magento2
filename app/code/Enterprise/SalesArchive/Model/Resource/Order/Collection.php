<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Order archive collection
 *
 * @category    Enterprise
 * @package     Enterprise_SalesArchive
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_SalesArchive_Model_Resource_Order_Collection extends Magento_Sales_Model_Resource_Order_Grid_Collection
{
    /**
     * Collection initialization
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setMainTable('enterprise_sales_order_grid_archive');
    }

    /**
     * Generate select based on order grid select for getting archived order fields.
     *
     * @param Zend_Db_Select $gridSelect
     * @return Zend_Db_Select
     */
    public function getOrderGridArchiveSelect(Zend_Db_Select $gridSelect)
    {
        $select = clone $gridSelect;
        $select->reset('from');
        $select->from(array('main_table' => $this->getTable('enterprise_sales_order_grid_archive')), array());
        return $select;
    }
}
