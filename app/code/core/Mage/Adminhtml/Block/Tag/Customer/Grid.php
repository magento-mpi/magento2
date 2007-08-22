<?php
/**
 * Child Of Mage_Adminhtml_Block_Tag_Customer
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Tag_Customer_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
            ->getCustomerCollection()
            ->addTagFilter($tagId)
            ->setCountAttribute('DISTINCT tr.customer_id')
            ->addGroupByCustomer();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _afterLoadCollection()
    {
        $this->getCollection()->addProductName();
        return parent::_afterLoadCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('customer_id', array(
            'header'        => __('ID'),
            'width'         => '50px',
            'align'         => 'right',
            'index'         => 'entity_id',
        ));

        $this->addColumn('firstname', array(
            'header'    => __('First Name'),
            'index'     => 'firstname',
        ));

        $this->addColumn('lastname', array(
            'header'    => __('Last Name'),
            'index'     => 'lastname',
        ));

        $this->addColumn('product', array(
            'header'    => __('Product Name'),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'product',
        ));

        $this->addColumn('product_sku', array(
            'header'    => __('Product SKU'),
            'filter'    => false,
            'sortable'  => false,
            'width'     => '50px',
            'align'     => 'right',
            'index'     => 'product_sku',
        ));

        return parent::_prepareColumns();
    }

    protected function getRowUrl($row)
    {
        return Mage::getUrl('*/customer/edit', array('id' => $row->getCustomerId()));
    }
}