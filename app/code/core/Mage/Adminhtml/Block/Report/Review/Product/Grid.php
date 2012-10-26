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
 * Adminhtml reviews by products report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Review_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('gridProducts');
        $this->setDefaultSort('review_cnt');
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Mage_Reports_Model_Resource_Review_Product_Collection')
            ->joinReview();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('entity_id', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('ID'),
            'width'     =>'50px',
            'index'     =>'entity_id'
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('Mage_Reports_Helper_Data')->__('Product Name'),
            'index'     => 'name'
        ));

        $this->addColumn('review_cnt', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Number of Reviews'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'review_cnt'
        ));

        $this->addColumn('avg_rating', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Avg. Rating'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'avg_rating'
        ));

        $this->addColumn('avg_rating_approved', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Avg. Approved Rating'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'avg_rating_approved'
        ));

        $this->addColumn('last_created', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Last Review'),
            'width'     =>'150px',
            'index'     =>'last_created',
            'type'      =>'datetime'
        ));

        $this->addColumn('action', array(
            'header'    => Mage::helper('Mage_Reports_Helper_Data')->__('Action'),
            'width'     => '100px',
            'align'     => 'center',
            'filter'    => false,
            'sortable'  => false,
            'renderer'  => 'Mage_Adminhtml_Block_Report_Grid_Column_Renderer_Product',
            'is_system' => true
        ));

        $this->setFilterVisibility(false);

        $this->addExportType('*/*/exportProductCsv', Mage::helper('Mage_Reports_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportProductExcel', Mage::helper('Mage_Reports_Helper_Data')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_product_review/', array('productId' => $row->getId()));
    }
}
