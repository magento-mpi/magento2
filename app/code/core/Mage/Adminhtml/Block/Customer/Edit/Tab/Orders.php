<?php
/**
 * Adminhtml customer orders grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_Orders extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customerOrdersGrid');
        $this->setDefaultSort('created_at', 'desc');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('sales/order_collection')
            ->addAttributeToSelect('increment_id')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('grand_total')
            ->addAttributeToSelect('store_id')
            ->joinAttribute('shipping_entity_id', 'order_address/entity_id', 'entity_id', 'parent_id')
            ->joinAttribute('shipping_address_type', 'order_address/address_type', 'shipping_entity_id')
            ->addAttributeToFilter('shipping_address_type', 'shipping')
            ->joinAttribute('shipping_firstname', 'order_address/firstname', 'shipping_entity_id')
            ->joinAttribute('shipping_lastname', 'order_address/lastname', 'shipping_entity_id')

        // the following line works, it is commented just to show some orders for any customers
        // because we haven't enough orders to show for each customer now
        // uncomment it to show only selected customer's orders

        // ->addAttributeToFilter('customer_id', $this->getRequest()->id)

            ->joinField('store_name', 'core/store', 'name', 'store_id=store_id', array('language_code'=>'en'));
        ;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        // Order Number, Date, Shipped To, Total, Status
        $this->addColumn('id', array(
            'header' => __('Order #'),
//            'width' => 5,
            'align' => 'center',
            'sortable' => true,
            'index' => 'increment_id',
        ));
        $this->addColumn('created_at', array(
            'header'    => __('Date'),
//            'width'     => 20,
            'index'     => 'created_at',
            'type'      => 'date',
        ));
        $this->addColumn('shipped_to', array(
            'header' => __('Ship To'),
            'index' => array('shipping_firstname','shipping_lastname'),
            'type' => 'concat',
            'separator' => ' ',
        ));
        $this->addColumn('grand_total', array(
            'header' => __('Order Total'),
            'index' => 'grand_total',
            'type'  => 'currency',
        ));
        $this->addColumn('store', array(
            'header' => __('Bought From'),
            'index' => 'store_name',
        ));
        $this->addColumn('action', array(
            'header' => '&nbsp;',
            'align' => 'center',
            'format' => '<a href="'.Mage::getUrl('*/sales_order/view/id/$entity_id').'">'.__('View').'</a>',
            'index' => 'entity_id',
            'sortable' => false,
            'filter' => false,
        ));

//        $this->addExportType('*/*/exportCsv', __('CSV'));
//        $this->addExportType('*/*/exportXml', __('XML'));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return Mage::getUrl('*/*/orders', array('_current'=>true));
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection() && $column->getFilter()->getValue()) {
            $this->getCollection()->addAttributeToFilter($column->getIndex(), $column->getFilter()->getCondition());
        }
        return $this;
    }
    
}