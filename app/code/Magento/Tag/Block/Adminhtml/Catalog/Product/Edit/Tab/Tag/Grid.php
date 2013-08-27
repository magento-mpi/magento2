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
 * Products tags grid
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Magento_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag_Grid extends Magento_Backend_Block_Widget_Grid_Extended
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('tag_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('Magento_Tag_Model_Tag')
            ->getResourceCollection()
            ->addProductFilter($this->getProductId())
            ->addPopularity();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _afterLoadCollection()
    {
        return parent::_afterLoadCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    => __('Tag'),
            'index'     => 'name',
        ));

        $this->addColumn('popularity', array(
            'header'        => __('Uses'),
            'width'         => '50px',
            'align'         => 'right',
            'index'         => 'popularity',
            'type'          => 'number',
        ));

        $this->addColumn('status', array(
            'header'    => __('Status'),
            'width'     => '90px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array(
                Magento_Tag_Model_Tag::STATUS_DISABLED => __('Disabled'),
                Magento_Tag_Model_Tag::STATUS_PENDING  => __('Pending'),
                Magento_Tag_Model_Tag::STATUS_APPROVED => __('Approved'),
            ),
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/tag/edit', array(
            'tag_id'        => $row->getId(),
            'product_id'    => $this->getProductId(),
        ));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/catalog_product/tagGrid', array(
            '_current'      => true,
            'id'            => $this->getProductId(),
            'product_id'    => $this->getProductId(),
        ));
    }
}
