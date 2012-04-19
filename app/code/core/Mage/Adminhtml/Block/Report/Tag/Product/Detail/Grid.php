<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml tags detail for product report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Tag_Product_Detail_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('grid');
    }

    protected function _prepareCollection()
    {

        $collection = Mage::getResourceModel('Mage_Reports_Model_Resource_Tag_Product_Collection');

        $collection->addTagedCount()
            ->addProductFilter($this->getRequest()->getParam('id'))
            ->addStatusFilter(Mage::getModel('Mage_Tag_Model_Tag')->getApprovedStatus())
            ->addStoresVisibility()
            ->setActiveFilter()
            ->addGroupByTag()
            ->setRelationId();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('tag_name', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Tag Name'),
            'index'     =>'tag_name'
        ));

        $this->addColumn('taged', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Tag Use'),
            'index'     =>'taged',
            'align'     => 'right'
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('visible', array(
                'header'    => Mage::helper('Mage_Reports_Helper_Data')->__('Visible In'),
                'sortable'  => false,
                'index'     => 'stores',
                'type'      => 'store',
                'store_view'=> true
            ));
        }

        $this->addExportType('*/*/exportProductDetailCsv', Mage::helper('Mage_Reports_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportProductDetailExcel', Mage::helper('Mage_Reports_Helper_Data')->__('Excel XML'));

        $this->setFilterVisibility(false);

        return parent::_prepareColumns();
    }

}

