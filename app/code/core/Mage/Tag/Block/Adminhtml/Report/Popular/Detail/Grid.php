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
 * Adminhtml tags detail for product report grid block
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tag_Block_Adminhtml_Report_Popular_Detail_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('tag_grid');
    }

    /**
     * Prepare collection for grid
     *
     * @return Mage_Tag_Block_Adminhtml_Report_Popular_Detail_Grid
     */
    protected function _prepareCollection()
    {
        /* @var $collection Mage_Tag_Model_Resource_Reports_Customer_Collection */
        $collection = Mage::getResourceModel('Mage_Tag_Model_Resource_Reports_Customer_Collection');
        $collection->addStatusFilter(Mage::getModel('Mage_Tag_Model_Tag')->getApprovedStatus())
            ->addTagFilter($this->getRequest()->getParam('id'))
            ->addProductToSelect();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Form columns for the grid
     *
     * @return Mage_Tag_Block_Adminhtml_Report_Popular_Detail_Grid
     */
    protected function _prepareColumns()
    {

        $this->addColumn('firstname', array(
            'header'    =>Mage::helper('Mage_Tag_Helper_Data')->__('First Name'),
            'index'     =>'firstname'
        ));

        $this->addColumn('lastname', array(
            'header'    =>Mage::helper('Mage_Tag_Helper_Data')->__('Last Name'),
            'index'     =>'lastname'
        ));

        $this->addColumn('product', array(
            'header'    =>Mage::helper('Mage_Tag_Helper_Data')->__('Product Name'),
            'index'     =>'product_name'
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('added_in', array(
                'header'    => Mage::helper('Mage_Tag_Helper_Data')->__('Submitted In'),
                'index'     => 'added_in',
                'type'      => 'store',
                'store_view'=> true
            ));
        }

        $this->setFilterVisibility(false);

        $this->addExportType('*/*/exportTagDetailCsv', Mage::helper('Mage_Tag_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportTagDetailExcel', Mage::helper('Mage_Tag_Helper_Data')->__('Excel XML'));

        return parent::_prepareColumns();
    }

}
