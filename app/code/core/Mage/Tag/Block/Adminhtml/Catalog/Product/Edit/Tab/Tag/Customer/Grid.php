<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * List of customers tagged a product
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method     Mage_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag_Customer_Grid setUseAjax() setUseAjax(bool $flag)
 * @method     int getProductId() getProductId()
 */
class Mage_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag_Customer_Grid extends Mage_Backend_Block_Widget_Grid
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
        if (Mage::helper('Mage_Catalog_Helper_Data')->isModuleEnabled('Mage_Tag')) {
            $collection = Mage::getModel('Mage_Tag_Model_Tag')
                ->getCustomerCollection()
                ->addProductFilter($this->getProductId())
                ->addGroupByTag()
                ->addDescOrder();

            $this->setCollection($collection);
        }
        return parent::_prepareCollection();
    }

    protected function _afterLoadCollection()
    {
        return parent::_afterLoadCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('firstname', array(
            'header' => Mage::helper('Mage_Catalog_Helper_Data')->__('First Name'),
            'index'  => 'firstname',
        ));

        $this->addColumn('lastname', array(
            'header' => Mage::helper('Mage_Catalog_Helper_Data')->__('Last Name'),
            'index'  => 'lastname',
        ));

        $this->addColumn('email', array(
            'header' => Mage::helper('Mage_Catalog_Helper_Data')->__('Email'),
            'index'  => 'email',
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('Mage_Catalog_Helper_Data')->__('Tag Name'),
            'index'  => 'name',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/customer/edit', array('id' => $row->getCustomerId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/catalog_product/tagCustomerGrid', array(
            '_current'   => true,
            'id'         => $this->getProductId(),
            'product_id' => $this->getProductId(),
        ));
    }
}
