<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order Invoices grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_View_Tab_Invoices
    extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('order_invoices');
        $this->setUseAjax(true);
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'Mage_Sales_Model_Resource_Order_Invoice_Grid_Collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass())
            ->addFieldToSelect('entity_id')
            ->addFieldToSelect('created_at')
            ->addFieldToSelect('order_id')
            ->addFieldToSelect('increment_id')
            ->addFieldToSelect('state')
            ->addFieldToSelect('grand_total')
            ->addFieldToSelect('base_grand_total')
            ->addFieldToSelect('store_currency_code')
            ->addFieldToSelect('base_currency_code')
            ->addFieldToSelect('order_currency_code')
            ->addFieldToSelect('billing_name')
            ->setOrderFilter($this->getOrder())
        ;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', array(
            'header'    => Mage::helper('Mage_Sales_Helper_Data')->__('Invoice'),
            'index'     => 'increment_id',
            'header_css_class'  => 'col-invoice-number',
            'column_css_class'  => 'col-invoice-number'
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('Mage_Sales_Helper_Data')->__('Bill-to Name'),
            'index' => 'billing_name',
            'header_css_class'  => 'col-name',
            'column_css_class'  => 'col-name'
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('Mage_Sales_Helper_Data')->__('Invoice Date'),
            'index'     => 'created_at',
            'type'      => 'datetime',
            'header_css_class'  => 'col-period',
            'column_css_class'  => 'col-period'
        ));

        $this->addColumn('state', array(
            'header'    => Mage::helper('Mage_Sales_Helper_Data')->__('Status'),
            'index'     => 'state',
            'type'      => 'options',
            'options'   => Mage::getModel('Mage_Sales_Model_Order_Invoice')->getStates(),
            'header_css_class'  => 'col-status',
            'column_css_class'  => 'col-status'
        ));

        $this->addColumn('base_grand_total', array(
            'header'    => Mage::helper('Mage_Customer_Helper_Data')->__('Amount'),
            'index'     => 'base_grand_total',
            'type'      => 'currency',
            'currency'  => 'base_currency_code',
            'header_css_class'  => 'col-qty',
            'column_css_class'  => 'col-qty'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Retrieve order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/sales_order_invoice/view',
            array(
                'invoice_id'=> $row->getId(),
                'order_id'  => $row->getOrderId()
            )
        );
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/invoices', array('_current' => true));
    }


    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('Mage_Sales_Helper_Data')->__('Invoices');
    }

    public function getTabTitle()
    {
        return Mage::helper('Mage_Sales_Helper_Data')->__('Order Invoices');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
