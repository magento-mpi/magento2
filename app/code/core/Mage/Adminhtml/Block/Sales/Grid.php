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
        $this->setId('salesGrid');
    }

    protected function _initCollection()
    {
        $collection = Mage::getResourceModel('sales/order_collection')
			->addAttributeSelect('self/created_at')
			->addAttributeSelect('self/grand_total')
			->addAttributeSelect('self/created_at')
			->addAttributeSelect('self/status')
			->addAttributeSelect('self/currency_code')
//			->addAttributeSelect('address/address_type')
			// ->addAttributeFilter('address/address_type', 'shipping')
			// ->addAttributeSelect('address/firstname')
			// ->addAttributeSelect('address/lastname')
        ;
        $this->setCollection($collection);
    }

    protected function _beforeToHtml()
    {
        $this->addColumn('id', array(
            'header' => __('id'),
            'width' => 5,
            'align' => 'center',
            'sortable' => true,
            'index' => 'order_id'
        ));

        // Order Number, Date, Shipped To, Total, Status

        $this->addColumn('created_at', array(
            'header'    => __('Created At'),
            'index'     => 'created_at',
            'type'      => 'date'
        ));
        $this->addColumn('firstname', array(
            'header' => __('Shipped To'),
            'index' => 'firstname',
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
            'format' => '<a href="'.Mage::getUrl('*/*/edit/id/$order_id').'">'.__('edit').'</a>',
            'index' => 'order_id',
            'sortable' => false,
        ));

        $this->_initCollection();
        return parent::_beforeToHtml();
    }
}