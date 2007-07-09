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
//            ->addAttributeToSelect('product_id')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
        ;

        if ($this->getCategoryId()) {
            $collection->addCategoryFilter($this->getCategoryId());
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array('header'=>__('id'), 'align'=>'center', 'sortable'=>false, 'index'=>'product_id'));
        $this->addColumn('sku', array('header'=>__('sku'), 'align'=>'center', 'index'=>'sku'));
        $this->addColumn('name', array('header'=>__('name'), 'index'=>'name'));
        $this->addColumn('action', array('header'=>__('Action'), 'index' => 'product_id', 'sortable' => false, 'filter' => false));

        return parent::_prepareColumns();
    }
}