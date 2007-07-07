<?php
/**
 * Adminhtml customer grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Customer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customerGrid');
        #$this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addAttributeToSelect('firstname')
            ->addAttributeToSelect('lastname')
            ->addAttributeToSelect('email');
            
        $collection
            ->joinAttribute('shipping_postcode', 'default_shipping', 'customer_address/postcode')
            ->joinAttribute('shipping_city', 'default_shipping', 'customer_address/city');
        
        #$collection->addAttributeToSort('shipping_firstname');
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
        $this->addColumn('email', array(
            'header'    =>__('email'), 
            'width'     =>40, 
            'align'     =>'center', 
            'index'     =>'email'
        ));
        $this->addColumn('firstname', array(
            'header'    =>__('firstname'), 
            'index'     =>'firstname'
        ));
        $this->addColumn('lastname', array(
            'header'    =>__('lastname'), 
            'index'     =>'lastname'
        ));
        $this->addColumn('shipping_postcode', array(
            'header'    =>__('shipping_postcode'),
            'index'     =>'shipping_postcode',
        ));
        $this->addColumn('shipping_city', array(
            'header'    =>__('shipping_city'),
            'index'     =>'shipping_city',
        ));
        $this->addColumn('action', array(
            'header'    =>__('action'),
            'align'     =>'center',
            'format'    =>'<a href="'.Mage::getUrl('*/*/edit/id/$entity_id').'">'.__('edit').'</a>',
            'index'     =>'customer_id', 
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