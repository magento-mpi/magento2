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
 * Adminhtml sales order create block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Customer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_customer_grid');
        $this->setRowClickCallback('order.selectCustomer.bind(order)');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Mage_Customer_Model_Resource_Customer_Collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at')
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_regione', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left')
            ->joinField('store_name', 'core_store', 'name', 'store_id=store_id', null, 'left');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    =>Mage::helper('Mage_Sales_Helper_Data')->__('ID'),
            'width'     =>'50px',
            'index'     =>'entity_id',
            'align'     => 'right',
        ));
        $this->addColumn('name', array(
            'header'    =>Mage::helper('Mage_Sales_Helper_Data')->__('Name'),
            'index'     =>'name'
        ));
        $this->addColumn('email', array(
            'header'    =>Mage::helper('Mage_Sales_Helper_Data')->__('Email'),
            'width'     =>'150px',
            'index'     =>'email'
        ));
        $this->addColumn('Telephone', array(
            'header'    =>Mage::helper('Mage_Sales_Helper_Data')->__('Phone'),
            'width'     =>'100px',
            'index'     =>'billing_telephone'
        ));
        $this->addColumn('billing_postcode', array(
            'header'    =>Mage::helper('Mage_Sales_Helper_Data')->__('ZIP/Post Code'),
            'width'     =>'120px',
            'index'     =>'billing_postcode',
        ));
        $this->addColumn('billing_country_id', array(
            'header'    =>Mage::helper('Mage_Sales_Helper_Data')->__('Country'),
            'width'     =>'100px',
            'type'      =>'country',
            'index'     =>'billing_country_id',
        ));
        $this->addColumn('billing_regione', array(
            'header'    =>Mage::helper('Mage_Sales_Helper_Data')->__('State/Province'),
            'width'     =>'100px',
            'index'     =>'billing_regione',
        ));

        $this->addColumn('store_name', array(
            'header'    =>Mage::helper('Mage_Sales_Helper_Data')->__('Signed-up Point'),
            'align'     => 'center',
            'index'     =>'store_name',
            'width'     =>'130px',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $row->getId();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/loadBlock', array('block'=>'customer_grid'));
    }

}
