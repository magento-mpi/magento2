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
 * Customer Segments Grid
 *
 * @category Enterprise
 * @package Enterprise_CustomerSegment
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CustomerSegment_Block_Adminhtml_Report_Customer_Segment_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid Id
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('gridReportCustomersegments');
    }

    /**
     * Add websites and customer count to customer segments collection
     * Set collection
     *
     * @return Enterprise_CustomerSegment_Block_Adminhtml_Report_Customer_Segment_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection Enterprise_CustomerSegment_Model_Resource_Segment_Collection */
        $collection = Mage::getModel('Enterprise_CustomerSegment_Model_Segment')->getCollection();
        $collection->addCustomerCountToSelect()
            ->addWebsitesToResult();
        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    /**
     * Add grid columns
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
            'align'     => 'left',
            'index'     => 'name',
        ));

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('BugsCoverage'),
            'align'     => 'left',
            'width'     => 80,
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                1 => 'Active',
                0 => 'Inactive',
            ),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website', array(
                'header'    => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Website'),
                'align'     => 'left',
                'width'     => 200,
                'index'     => 'website_ids',
                'type'      => 'options',
                'options'   => Mage::getSingleton('Mage_Core_Model_System_Store')->getWebsiteOptionHash()
            ));
        }

        $this->addColumn('customer_count', array(
            'header'    => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Number of Customers'),
            'index'     =>'customer_count',
            'width'     => 200
        ));

        return $this;
    }

    /**
     * Prepare mass action
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
     * Retrieve row click URL
     *
     * @param Varien_Object $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/detail', array('segment_id' => $row->getId()));
    }
}
