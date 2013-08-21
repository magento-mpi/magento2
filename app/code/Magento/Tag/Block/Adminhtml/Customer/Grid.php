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
 * Child Of Magento_Tag_Block_Adminhtml_Customer
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Block_Adminhtml_Customer_Grid extends Magento_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        parent::_construct();
        if (Mage::registry('current_tag')) {
            $this->setId('tag_customer_grid' . Mage::registry('current_tag')->getId());
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
        return $this->getUrl('*/*/customer', array('_current' => true));
    }

    protected function _prepareCollection()
    {
        $tagId = Mage::registry('current_tag')->getId();
        $storeId = Mage::registry('current_tag')->getStoreId();
        $collection = Mage::getModel('Magento_Tag_Model_Tag')
            ->getCustomerCollection()
            ->addTagFilter($tagId)
            ->setCountAttribute('tr.tag_relation_id')
            ->addStoreFilter($storeId)
            ->addGroupByCustomerProduct();

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
            'width'         => 50,
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
            'header'    => __('Product'),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'product',
        ));

        $this->addColumn('product_sku', array(
            'header'    => __('SKU'),
            'filter'    => false,
            'sortable'  => false,
            'width'     => 50,
            'align'     => 'right',
            'index'     => 'product_sku',
        ));



        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/customer/edit', array('id' => $row->getId()));
    }

}
