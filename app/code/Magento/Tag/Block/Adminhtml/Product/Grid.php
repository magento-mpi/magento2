<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Child Of Magento_Tag_Block_Adminhtml_Product
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Block_Adminhtml_Product_Grid extends Magento_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        parent::_construct();
        if (Mage::registry('current_tag')) {
            $this->setId('tag_product_grid' . Mage::registry('current_tag')->getId());
        }
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }

    /*
     * Retrieves Grid Url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/product', array('_current' => true));
    }

    protected function _prepareCollection()
    {
        $tagId = Mage::registry('current_tag')->getId();
        $storeId = Mage::registry('current_tag')->getStoreId();
        $collection = Mage::getModel('Magento_Tag_Model_Tag')
            ->getEntityCollection()
            ->addTagFilter($tagId)
            ->addCustomerFilter(array('null' => false))
            ->addStoreFilter($storeId)
            ->addPopularity($tagId);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _afterLoadCollection()
    {
        return parent::_afterLoadCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('product_id', array(
            'header'        => __('ID'),
            'width'         => '50px',
            'align'         => 'right',
            'index'         => 'entity_id',
        ));

        $this->addColumn('name', array(
            'header'    => __('Product'),
            'index'     => 'name',
        ));

        $this->addColumn('popularity', array(
            'header'        => __('Uses'),
            'width'         => '50px',
            'align'         => 'right',
            'index'         => 'popularity',
            'type'          => 'number'
        ));

        $this->addColumn('sku', array(
            'header'    => __('SKU'),
            'filter'    => false,
            'sortable'  => false,
            'width'     => 50,
            'align'     => 'right',
            'index'     => 'sku',
        ));

        return parent::_prepareColumns();
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getIndex() == 'popularity') {
            $this->getCollection()->addPopularityFilter($column->getFilter()->getCondition());
            return $this;
        } else {
            return parent::_addColumnFilterToCollection($column);
        }
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_product/edit', array('id' => $row->getProductId()));
    }

}
