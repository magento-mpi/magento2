<?php
/**
 * Adminhtml products in carts report grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmytro Vasylenko <dimav@varien.com>
 */
class Mage_Adminhtml_Block_Report_Shopcart_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('gridProducts');
    }

    protected function _prepareCollection()
    {          
        $collection = Mage::getResourceModel('reports/product_collection')
          ->addAttributeToSelect('price');
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _afterLoadCollection()
    {
        $this->getCollection()
            ->addCartsCount()
            ->addOrdersCount();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    =>__('ID'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'entity_id'
        ));
        
        $this->addColumn('name', array(
            'header'    =>__('Product Name'),
            'index'     =>'name'
        ));    
        
        $this->addColumn('price', array(
            'header'    =>__('Price'),
            'width'     =>'70px',
            'type'      =>'currency',
            'align'     =>'right',
            'currency_code' => (string) Mage::getStoreConfig('general/currency/base'),
            'index'     =>'price'
        ));
 
        $this->addColumn('carts', array(
            'header'    =>__('Carts'),
            'width'     =>'70px',
            'sortable'  =>false,
            'align'     =>'right',
            'index'     =>'carts'
        ));
        
        $this->setFilterVisibility(false); 
                      
        return parent::_prepareColumns();
    }    
}
