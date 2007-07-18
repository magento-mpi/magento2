<?php
/**
 * Adminhtml tagged products grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Adminhtml_Block_Tag_Grid_Products extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('tag/product_collection')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
        ;
        if ($tagId = $this->getRequest()->getParam('tag_id')) {
            $collection->addTagFilter($tagId);
        }
        if ($customerId = $this->getRequest()->getParam('customer_id')) {
            $collection->addCustomerFilter($customerId);
        }
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => __('ID'),
            'align'     => 'center',
            'width'     => '60px',
            'sortable'  => false,
            'index'     => 'product_id'
        ));
        $this->addColumn('sku', array(
            'header'    => __('SKU'),
            'align'     => 'center',
            'index'     => 'sku'
        ));
        $this->addColumn('name', array(
            'header'    => __('Name'),
            'index'     => 'name'
        ));
        $this->addColumn('tags', array(
            'header'    => __('Tags'),
            'index'     => 'tags',
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => 'adminhtml/tag_grid_column_renderer_tags'
        ));
        $this->addColumn('action', array(
            'header'    => __('Action'),
            'align'     => 'center',
            'width'     => '120px',
            'format'    => '<a href="'.Mage::getUrl('*/*/customers/product_id/$product_id').'">'.__('View Customers').'</a>',
            'filter'    => false,
            'sortable'  => false,
            'is_system' => true
        ));

        return parent::_prepareColumns();
    }
}
