<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Segments grid
 *
 * @category   Enterprise
 * @package    Enterprise_CustomerSegment
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CustomerSegment_Block_Adminhtml_Report_Customer_Segment_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('gridReportCustomersegments');
    }

    /**
     * Prepare report collection
     *
     * @return Enterprise_CustomerSegment_Block_Adminhtml_Report_Customer_Segment_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('Enterprise_CustomerSegment_Model_Segment')->getCollection();
        $collection->addCustomerCountToSelect();
        $collection->addWebsitesToResult();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Filter number of customers column
     *
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return Enterprise_CustomerSegment_Block_Adminhtml_Report_Customer_Segment_Grid
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'customer_count') {
            if ($column->getFilter()->getValue() !== null) {
                $this->getCollection()->addCustomerCountFilter($column->getFilter()->getValue());
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare grid columns
     *
     * @return Enterprise_CustomerSegment_Block_Adminhtml_Report_Customer_Segment_Grid
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn('segment_id', array(
            'header'    => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('ID'),
            'align'     =>'right',
            'width'     => 50,
            'index'     => 'segment_id',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Segment Name'),
            'align'     =>'left',
            'index'     => 'name',
        ));

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Status'),
            'align'     => 'left',
            'width'     => 80,
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                1 => 'Active',
                0 => 'Inactive',
            ),
        ));

        $this->addColumn('website', array(
            'header'    => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Website'),
            'align'     =>'left',
            'width'     => 200,
            'index'     => 'website_ids',
            'type'      => 'options',
            'options'   => Mage::getSingleton('Mage_Adminhtml_Model_System_Store')->getWebsiteOptionHash()
        ));

        $this->addColumn('customer_count', array(
            'header'    => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Number of Customers'),
            'index'     =>'customer_count',
            'width'     => 200
        ));

        return $this;
    }

    /**
     * Prepare massasction
     *
     * @return Enterprise_CustomerSegment_Block_Adminhtml_Report_Customer_Segment_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('segment_id');
        $this->getMassactionBlock()->addItem('view', array(
            'label'=> Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('View Combined Report'),
            'url'  => $this->getUrl('*/*/detail', array('_current'=>true)),
            'additional' => array(
                'visibility' => array(
                         'name'     => 'view_mode',
                         'type'     => 'select',
                         'class'    => 'required-entry',
                         'label'    => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Set'),
                         'values'   => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->getOptionsArray()
                     )
             )
        ));
        return $this;
    }

    /**
     * Return url for current row
     *
     * @param Enterprise_CustomerSegment_Model_Segment $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/detail', array('segment_id' => $row->getId()));
    }
}
