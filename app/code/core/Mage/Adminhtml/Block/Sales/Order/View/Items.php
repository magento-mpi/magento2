<?php
/**
 * Adminhtml order items grid
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_View_Items extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('order_items_grid');
        $this->setDefaultSort('entity_id', 'asc');
        $this->setSortable(false);
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('sales/order_item_collection')
            ->addAttributeToSelect('*')
            ->setOrderFilter(Mage::registry('sales_order')->getId())
        ;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('product_id', array(
            'header' => __('Product ID'),
            'align' => 'center',
            'index' => 'product_id',
        ));

        $this->addColumn('sku', array(
            'header' => __('SKU'),
            'index' => 'sku',
        ));

        $this->addColumn('name', array(
            'header' => __('Product Name'),
            'index' => 'name',
        ));

        $this->addColumn('status', array(
            'header' => __('Item Status'),
            'getter' => 'getStatus',
        ));

        $this->addColumn('qty_ordered', array(
            'header' => __('Qty Ordered'),
            'index' => 'qty_ordered',
            'type' => 'number',
        ));

        $this->addColumn('qty_backordered', array(
            'header' => __('Qty Backordered'),
            'index' => 'qty_backordered',
            'type' => 'number',
        ));

        $this->addColumn('qty_shipped', array(
            'header' => __('Qty Shipped'),
            'index' => 'qty_shipped',
            'type' => 'number',
        ));

        $this->addColumn('qty_returned', array(
            'header' => __('Qty Returned'),
            'index' => 'qty_returned',
            'type' => 'number',
        ));

        $this->addColumn('qty_canceled', array(
            'header' => __('Qty Cancelled'),
            'index' => 'qty_canceled',
            'type' => 'number',
        ));

        return parent::_prepareColumns();
    }

}
