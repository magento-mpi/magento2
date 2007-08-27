<?php
/**
 * Adminhtml items in carts report grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmytro Vasylenko <dimav@varien.com>
 */
class Mage_Adminhtml_Block_Report_Shopcart_Customer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('grid');
    }

    protected function _prepareCollection()
    {          
        $collection = Mage::getResourceModel('reports/customer_collection')
          ->addAttributeToSelect('firstname')
          ->addAttributeToSelect('lastname');
                  
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _afterLoadCollection()
    {
        $this->getCollection()->addCartInfo();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    =>__('ID'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'entity_id'
        ));
        
        $this->addColumn('firstname', array(
            'header'    =>__('First Name'),
            'index'     =>'firstname'
        ));
        
        $this->addColumn('lastname', array(
            'header'    =>__('Last Name'),
            'index'     =>'lastname'
        ));
        
        $this->addColumn('items', array(
            'header'    =>__('Items in Cart'),
            'width'     =>'70px',
            'sortable'  =>false,
            'align'     =>'right',
            'index'     =>'items'
        ));
 
        $this->addColumn('total', array(
            'header'    =>__('Total'),
            'width'     =>'70px',
            'sortable'  =>false,
            'type'      =>'currency',
            'align'     =>'right',
            'currency_code' => (string) Mage::getStoreConfig('general/currency/base'),
            'index'     =>'total'
        ));
        
        $this->setFilterVisibility(false); 
                      
        return parent::_prepareColumns();
    }    
}
