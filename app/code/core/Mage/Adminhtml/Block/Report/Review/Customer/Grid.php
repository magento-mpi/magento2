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
 * Adminhtml reviews by customers report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Review_Customer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('customers_grid');
        $this->setDefaultSort('review_cnt');
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Mage_Reports_Model_Resource_Review_Customer_Collection')
            ->joinCustomers();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('customer_name', array(
            'header'    => Mage::helper('Mage_Reports_Helper_Data')->__('Customer Name'),
            'index'     => 'customer_name',
            'default'   => Mage::helper('Mage_Reports_Helper_Data')->__('Guest'),
        ));

        $this->addColumn('review_cnt', array(
            'header'    => Mage::helper('Mage_Reports_Helper_Data')->__('Number Of Reviews'),
            'width'     => '40px',
            'align'     => 'right',
            'index'     => 'review_cnt'
        ));

        $this->addColumn('action', array(
            'header'    => Mage::helper('Mage_Reports_Helper_Data')->__('Action'),
            'width'     => '100px',
            'align'     => 'center',
            'filter'    => false,
            'sortable'  => false,
            'renderer'  => 'Mage_Adminhtml_Block_Report_Grid_Column_Renderer_Customer',
            'is_system' => true
        ));

        $this->setFilterVisibility(false);

        $this->addExportType('*/*/exportCustomerCsv', Mage::helper('Mage_Reports_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportCustomerExcel', Mage::helper('Mage_Reports_Helper_Data')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_product_review', array('customerId' => $row->getCustomerId()));
    }
}
