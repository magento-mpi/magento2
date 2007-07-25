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
class Mage_Adminhtml_Block_Catalog_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('productGrid');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('qty')
            ->addAttributeToSelect('price');

        if ($this->getCategoryId()) {
            $collection->addCategoryFilter($this->getCategoryId());
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', 
            array(
                'header'=> __('ID'), 
                'width' => '50px', 
                'index' => 'entity_id',
        ));
        $this->addColumn('name', 
            array(
                'header'=> __('Name'), 
                'index' => 'name',
        ));
        $this->addColumn('sku', 
            array(
                'header'=> __('SKU'),
                'width' => '80px',
                'index' => 'sku',
        ));
        $this->addColumn('price', 
            array(
                'header'=> __('Price'),
                'type'  => 'currency',
                'index' => 'price',
        ));
        $this->addColumn('qty', 
            array(
                'header'=> __('Qty'),
                'width' => '50px',
                'index' => 'qty',
        ));
        $this->addColumn('status', 
            array(
                'header'=> __('Status'),
                'width' => '50px',
                'index' => 'status',
        ));
        $this->addColumn('rating', 
            array(
                'header'=> __('Rating'),
                'width' => '100px',
                'index' => 'rating',
        ));
        $this->addColumn('category', 
            array(
                'header'=> __('Categories'),
                'width' => '150px',
                'filter'=> false,
                'index' => 'category',
        ));
        $this->addColumn('stores', 
            array(
                'header'=> __('Stores'),
                'width' => '100px',
                'filter'=> false,
                'index' => 'stores',
        ));
        
        $this->addExportType('*/*/exportCsv', __('CSV'));
        $this->addExportType('*/*/exportXml', __('XML'));

        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row)
    {
        return Mage::getUrl('*/*/edit', array('id'=>$row->getId()));
    }
}
