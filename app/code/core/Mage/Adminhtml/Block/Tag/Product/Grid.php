<?php
/**
 * Child Of Mage_Adminhtml_Block_Tag_Product
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Tag_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('tag_grid' . Mage::registry('tagId'));
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
        $tagId = Mage::registry('tagId');
        $collection = Mage::getModel('tag/tag')
            ->getEntityCollection()
            ->addTagFilter($tagId)
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
            'header'    => __('Product Name'),
            'index'     => 'name',
        ));

        $this->addColumn('popularity', array(
            'header'        => __('Popularity'),
            'width'         => '50px',
            'align'         => 'right',
            'index'         => 'popularity',
        ));

        return parent::_prepareColumns();
    }

    protected function getRowUrl($row)
    {
        return Mage::getUrl('*/catalog_product/edit', array('id' => $row->getProductId()));
    }
}