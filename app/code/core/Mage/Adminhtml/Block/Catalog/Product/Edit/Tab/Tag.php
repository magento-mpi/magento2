<?php
/**
 * Products' tags grid
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Tag extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('tag_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $tagId = Mage::registry('tagId');
        $collection = Mage::getModel('tag/tag')
            ->getResourceCollection()
            ->addProductFilter($this->getProductId())
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
        $this->addColumn('name', array(
            'header'    => __('Tag Name'),
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
        return Mage::getUrl('*/tag/edit', array(
            'tag_id' => $row->getId(),
            'product_id' => $this->getProductId(),
        ));
    }

    public function getGridUrl()
    {
        return Mage::getUrl('*/catalog_product/tagGrid', array(
            '_current' => true,
            'id'       => $this->getProductId()
        ));
    }
}