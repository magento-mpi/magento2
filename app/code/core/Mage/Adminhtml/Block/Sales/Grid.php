<?php
/**
 * Adminhtml customer grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Adminhtml_Block_Sales_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('ordersGrid');
        $this->setDefaultSort('id');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('sales/order_collection')
            ->addAttributeToSelect('real_order_id')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('grand_total')
            ->addAttributeToSelect('currency_code')
            ->addAttributeToSelect('status')
            ->joinAttribute('shipping_entity_id', 'order_address/entity_id', 'parent_id')
            ->joinAttribute('shipping_address_type', 'order_address/address_type', 'shipping_entity_id')
            ->addAttributeToFilter('shipping_address_type', 'shipping')
            ->joinAttribute('shipping_firstname', 'order_address/firstname', 'shipping_entity_id')
            ->joinAttribute('shipping_lastname', 'order_address/lastname', 'shipping_entity_id')
            ->joinAttribute('payment_entity_id', 'order_payment/entity_id', 'parent_id')
            ->joinAttribute('payment_method', 'order_payment/method','payment_entity_id')
        ;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        // Order Number, Date, Shipped To, Total, Status
        $this->addColumn('id', array(
            'header' => __('id'),
            'width' => 5,
            'align' => 'center',
            'sortable' => true,
            'index' => 'entity_id'
        ));
        $this->addColumn('created_at', array(
            'header'    => __('Created At'),
            'index'     => 'created_at',
            'type'      => 'date'
        ));
        $this->addColumn('shipped_to', array(
            'header' => __('Shipped To'),
            'index' => array('shipping_firstname','shipping_lastname'),
            'type' => 'concat',
            'separator' => ' ',
        ));
        $this->addColumn('payment_method', array(
            'header' => __('Payment Method'),
            'index' => 'payment_method',
        ));
        $this->addColumn('grand_total', array(
            'header' => __('Total'),
            'index' => 'grand_total',
            'type'  => 'currency'
        ));
        $this->addColumn('status', array(
            'header' => __('Status'),
            'index' => 'status',
        ));
        $this->addColumn('action', array(
            'header' => __('action'),
            'align' => 'center',
            'format' => '<a href="'.Mage::getUrl('*/*/edit/id/$entity_id').'">'.__('edit').'</a>',
            'index' => 'order_id',
            'sortable' => false,
            'filter' => false,
        ));

        $this->setColumnFilter('id')
            ->setColumnFilter('created_at')
            ->setColumnFilter('firstname')
            ->setColumnFilter('grand_total')
            ->setColumnFilter('status')
        ;

        return parent::_prepareColumns();
    }
}