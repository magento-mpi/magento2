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
class Mage_Adminhtml_Block_Catalog_Category_Tab_Product extends Mage_Adminhtml_Block_Widget_Grid 
{
    public function __construct() 
    {
        parent::__construct();
        $this->setId('catalog_category_products');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('price');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('checkbox', array(
            'header'    =>__('ID'),
            'align'     =>'center',
            'sortable'  =>true,
            'index'     =>'entity_id'
        ));
        $this->addColumn('id', array(
            'header'    =>__('ID'),
            'align'     =>'center',
            'sortable'  =>true,
            'index'     =>'entity_id'
        ));
        $this->addColumn('name', array(
            'header'    =>__('Name'),
            'index'     =>'name'
        ));
        $this->addColumn('sku', array(
            'header'    =>__('SKU'),
            'index'     =>'sku'
        ));
        $this->addColumn('price', array(
            'header'    =>__('Price'),
            'align'     =>'center',
            'index'     =>'price'
        ));
        
        return parent::_prepareColumns();
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection() && $column->getFilter()->getValue()) {
            $this->getCollection()->addAttributeToFilter($column->getIndex(), $column->getFilter()->getCondition());
        }
        return $this;
    }
    
    public function getGridUrl()
    {
        return Mage::getUrl('*/*/grid');
    }
}
