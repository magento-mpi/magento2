<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Model\Resource\Order;

/**
 * Order archive collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Sales\Model\Resource\Order\Grid\Collection
{
    /**
     * Collection initialization
     *
     * @return void
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
        $select->from(['main_table' => $this->getTable('magento_sales_order_grid_archive')], []);
        return $select;
    }
}
