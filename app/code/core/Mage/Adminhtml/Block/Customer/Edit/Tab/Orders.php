<?php
/**
 * Adminhtml customer orders grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_Orders extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('ordersGrid');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addAttributeToSelect('firstname')
            ->addAttributeToSelect('lastname')
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at')
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing')
            ->joinField('billing_country_name', 'directory/country_name', 'name', 'country_id=billing_country_id', array('language_code'=>'en'));
        
        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    =>__('id'), 
            'width'     =>5, 
            'align'     =>'center', 
            'sortable'  =>true, 
            'index'     =>'entity_id'
        ));
        $this->addColumn('firstname', array(
            'header'    =>__('firstname'), 
            'index'     =>'firstname'
        ));
        $this->addColumn('lastname', array(
            'header'    =>__('lastname'), 
            'index'     =>'lastname'
        ));
        $this->addColumn('email', array(
            'header'    =>__('email'), 
            'width'     =>40, 
            'align'     =>'center', 
            'index'     =>'email'
        ));
        $this->addColumn('telephone', array(
            'header'    =>__('telephone'), 
            'align'     =>'center', 
            'index'     =>'billing_telephone'
        ));
        $this->addColumn('billing_postcode', array(
            'header'    =>__('postcode'),
            'index'     =>'billing_postcode',
        ));
        $this->addColumn('billing_country_name', array(
            'header'    =>__('country'),
            #'filter'    => 'adminhtml/customer_grid_filter_country',
            'index'     =>'billing_country_name',
        ));
        $this->addColumn('customer_since', array(
            'header'    =>__('customer since'),
            'type'      => 'date',
            'format'    => 'Y.m.d',
            'index'     =>'created_at',
        ));
        $this->addColumn('action', array(
            'header'    =>__('action'),
            'align'     =>'center',
            'format'    =>'<a href="'.Mage::getUrl('*/sales/edit/id/$entity_id').'">'.__('edit').'</a>',
            'filter'    =>false,
            'sortable'  =>false,
            'is_system' =>true
        ));
        
        $this->setColumnFilter('id')
            ->setColumnFilter('email')
            ->setColumnFilter('firstname')
            ->setColumnFilter('lastname');
        
        $this->addExportType('*/*/exportCsv', __('CSV'));
        $this->addExportType('*/*/exportXml', __('XML'));
        return parent::_prepareColumns();
    }
}