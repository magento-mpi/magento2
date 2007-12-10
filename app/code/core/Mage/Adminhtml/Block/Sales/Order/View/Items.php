<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml order items grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
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
        $collection = Mage::getResourceModel('sales/order_item_collection');
        /* @var $collection Mage_Sales_Model_Entity_Order_Item_Collection */
        $collection->addAttributeToSelect('*')
            ->setOrderFilter(Mage::registry('sales_order')->getId());
        $collection->getEntity()->setStore(Mage::registry('sales_order')->getStoreId());
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

        /*$this->addColumn('giftmessage', array(
            'header' => __('Gift Message'),
            'renderer' => 'adminhtml/sales_order_edit_items_grid_renderer_giftmessage',
            'store'     => Mage::registry('sales_order')->getStoreId()
        ));*/

        $this->addColumn('price', array(
            'header' => __('Price'),
            'getter' => 'getPriceFormatted',
        ));

        $this->addColumn('qty_ordered', array(
            'header' => __('Qty Ordered'),
            'index' => 'qty_ordered',
            'type' => 'number',
        ));



        // $this->addColumn('qty_backordered', array(
            // 'header' => __('Qty Backordered'),
            // 'index' => 'qty_backordered',
            // 'type' => 'number',
        // ));

        // $this->addColumn('qty_shipped', array(
            // 'header' => __('Qty Shipped'),
            // 'index' => 'qty_shipped',
            // 'type' => 'number',
        // ));

        // $this->addColumn('qty_returned', array(
            // 'header' => __('Qty Returned'),
            // 'index' => 'qty_returned',
            // 'type' => 'number',
        // ));

        $this->addColumn('qty_canceled', array(
            'header' => __('Qty Cancelled'),
            'index' => 'qty_canceled',
            'type' => 'number',
        ));

        $this->addColumn('status', array(
            'header' => __('Item Status'),
            'getter' => 'getStatus',
        ));

        $this->addColumn('discount_amount', array(
            'header' => __('Discount'),
            'getter' => 'getDiscountAmountFormatted',
        ));

        $this->addColumn('tax_amount', array(
            'header' => __('Tax Amount'),
            'getter' => 'getTaxAmountFormatted',
        ));

        $this->addColumn('row_total', array(
            'header' => __('Subtotal'),
            'getter' => 'getRowTotalFormatted',
        ));


        return parent::_prepareColumns();
    }

}
