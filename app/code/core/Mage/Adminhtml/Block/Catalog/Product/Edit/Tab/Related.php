<?php
/**
 * Related products admin grid
 *
 * @package     Mage
 * @subpackage  Adminhmtl
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnryi <mitch@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Related extends Mage_Adminhtml_Block_Widget_Grid 
{
    public function __construct() 
    {
        parent::__construct();
        $this->setId('related_product_grid');
        $this->setDefaultFilter(array('in_products'=>1));
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
            	$this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
            }
            else {
                if($productIds) {
                	$this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
            	}
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
    
    protected function _prepareCollection()
    {
       
        $collection = Mage::getResourceModel('catalog/product_link_collection')
            ->setLinkType('relation')
            ->setProductId(Mage::registry('product')->getId())
            ->setStoreId(Mage::registry('product')->getStoreId())
        	->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('price')
            ->addLinkAttributeToSelect('position')
            ->addLinkAttributeToSelect('qty')
            ->useProductItem();

        $this->setCollection($collection);
        
        

        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('in_products', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_products',
            'values'    => $this->_getSelectedProducts(),
            'align'     => 'center',
            'index'     => 'entity_id'
        ));
        
        $this->addColumn('id', array(
            'header'    => __('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header'    => __('Name'),
            'index'     => 'name'
        ));
        $this->addColumn('sku', array(
            'header'    => __('SKU'),
            'width'     => '80px',
            'index'     => 'sku'
        ));
        $this->addColumn('price', array(
            'header'    => __('Price'),
            'align'     => 'center',
            'type'      => 'currency',
            'index'     => 'price'
        ));
        
        $this->addColumn('qty', array(
            'header'    => __('Qty'),
            'name'    	=> 'qty',            
            'align'     => 'center',
            'type'      => 'number',
            'validate_class' => 'validate-number',
            'index'     => 'qty',
            'width'     => '60px',
            'editable'  => true
        ));
        
        $this->addColumn('position', array(
            'header'    => __('Position'),
            'name'    	=> 'position',
            'align'     => 'center',
            'type'      => 'number',
            'validate_class' => 'validate-number',
            'index'     => 'position',
            'width'     => '60px',
            'editable'  => true
        ));
        
         
        
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return Mage::getUrl('*/*/related', array('_current'=>true));
    }
    
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('products', null);
        
                
        if (!is_array($products)) {
            $products = Mage::registry('product')->getRelatedProductsLoaded()->getColumnValues('entity_id');
        }
        
        return $products;
    }
}