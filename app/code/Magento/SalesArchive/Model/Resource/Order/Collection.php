<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Order archive collection
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\SalesArchive\Model\Resource\Order;

class Collection extends \Magento\Sales\Model\Resource\Order\Grid\Collection
{
    /**
     * Collection initialization
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setMainTable('magento_sales_order_grid_archive');
    }

    /**
     * Generate select based on order grid select for getting archived order fields.
     *
     * @param \Zend_Db_Select $gridSelect
     * @return \Zend_Db_Select
     */
    public function getOrderGridArchiveSelect(\Zend_Db_Select $gridSelect)
    {
        $select = clone $gridSelect;
        $select->reset('from');
        $select->from(array('main_table' => $this->getTable('magento_sales_order_grid_archive')), array());
        return $select;
    }
}
