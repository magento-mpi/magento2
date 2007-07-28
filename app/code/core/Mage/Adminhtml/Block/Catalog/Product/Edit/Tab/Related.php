<?php
/**
 * Product in category grid
 *
 * @package     MAge
 * @subpackage  Adminhmtl
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Related extends Mage_Adminhtml_Block_Widget_Grid 
{
    public function __construct() 
    {
        parent::__construct();
        $this->setId('related_product_grid');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }
    
    protected function _initChildren()
    {
        parent::_initChildren();
        $this->setChild('switch_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Edit Sorting'),
                    'onclick'   => ''
                ))
        );
    }

    public function getMainButtonsHtml()
    {
        $html = $this->getChildHtml('switch_button');
        $html.= parent::getMainButtonsHtml();
        return $html;
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in category flag
        if ($column->getId() == 'in_relation_link') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
            	$this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
            }
            else {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
    
    protected function _prepareCollection()
    {
        $this->setDefaultFilter(array('in_relation_link'=>1));
        $collection = Mage::getResourceModel('catalog/product_link_collection')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('price')
            ->addLinkAttributeToSelect('position')
            ->useProductItem();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('products', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'products',
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
        
        $this->addColumn('position', array(
            'header'    => __('Position'),
            'name'    	=> 'position',
            'width'     => '140px',
            'align'     => 'center',
            'type'      => 'number',
            'index'     => 'position',
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
        $products = $this->getRequest()->getPost('products');
        
        if (is_null($products)) {
            $products = Mage::registry('product')->getRelatedProducts()->load()->getColumnValues('entity_id');
        }
        
        return $products;
    }
}