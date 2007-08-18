<?php
/**
 * List of customers tagged a product
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Tag_Customer extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('tag_customers_grid');
        $this->setDefaultSort('firstname');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $tagId = Mage::registry('tagId');
        $collection = Mage::getModel('tag/tag')
            ->getCustomerCollection()
            ->addProductFilter($this->getProductId())
            ->addGroupByTag();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _afterLoadCollection()
    {
        return parent::_afterLoadCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('firstname', array(
            'header'    => __('First Name'),
            'index'     => 'firstname',
        ));

        $this->addColumn('lastname', array(
            'header'        => __('Last Name'),
            'index'         => 'lastname',
        ));

        $this->addColumn('email', array(
            'header'        => __('Email'),
            'index'         => 'email',
        ));

        $this->addColumn('name', array(
            'header'        => __('Tag Name'),
            'index'         => 'name',
        ));

        return parent::_prepareColumns();
    }

    protected function getRowUrl($row)
    {
        return Mage::getUrl('*/tag/edit', array(
            'tag_id' => $row->getTagId(),
            'product_id' => $this->getProductId(),
        ));
    }

    public function getGridUrl()
    {
        return Mage::getUrl('*/catalog_product/tagCustomerGrid', array(
            '_current' => true,
            'id'       => $this->getProductId(),
            'product_id' => $this->getProductId(),
        ));
    }
}