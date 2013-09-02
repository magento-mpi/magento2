<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Segments Grid
 *
 * @category Magento
 * @package Magento_CustomerSegment
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomerSegment_Block_Adminhtml_Report_Customer_Segment_Grid
    extends Magento_Backend_Block_Widget_Grid_Extended
{
    /**
     * Customer segment data
     *
     * @var Magento_CustomerSegment_Helper_Data
     */
    protected $_customerSegmentData = null;

    /**
     * @param Magento_CustomerSegment_Helper_Data $customerSegmentData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param array $data
     */
    public function __construct(
        Magento_CustomerSegment_Helper_Data $customerSegmentData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        array $data = array()
    ) {
        $this->_customerSegmentData = $customerSegmentData;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    /**
     * Set grid Id
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('gridReportCustomersegments');
    }

    /**
     * Add websites and customer count to customer segments collection
     * Set collection
     *
     * @return Magento_CustomerSegment_Block_Adminhtml_Report_Customer_Segment_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection Magento_CustomerSegment_Model_Resource_Segment_Collection */
        $collection = Mage::getModel('Magento_CustomerSegment_Model_Segment')->getCollection();
        $collection->addCustomerCountToSelect()->addWebsitesToResult();
        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    /**
     * Add grid columns
     *
     * @return Magento_CustomerSegment_Block_Adminhtml_Report_Customer_Segment_Grid
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn('segment_id', array(
            'header'    => __('ID'),
            'index'     => 'segment_id',
            'header_css_class'  => 'col-id',
            'column_css_class'  => 'col-id'
        ));

        $this->addColumn('name', array(
            'header'    => __('Segment'),
            'index'     => 'name',
            'header_css_class'  => 'col-segment',
            'column_css_class'  => 'col-segment'
        ));

        $this->addColumn('is_active', array(
            'header'    => __('Status'),
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                1 => 'Active',
                0 => 'Inactive',
            ),
            'header_css_class'  => 'col-status',
            'column_css_class'  => 'col-status'
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website', array(
                'header'    => __('Website'),
                'index'     => 'website_ids',
                'type'      => 'options',
                'options'   => Mage::getSingleton('Magento_Core_Model_System_Store')->getWebsiteOptionHash(),
                'header_css_class'  => 'col-website',
                'column_css_class'  => 'col-website'
            ));
        }

        $this->addColumn('customer_count', array(
            'header'    => __('Customers'),
            'index'     =>'customer_count',
            'header_css_class'  => 'col-qty',
            'column_css_class'  => 'col-qty'
        ));

        return $this;
    }

    /**
     * Prepare mass action
     *
     * @return Magento_CustomerSegment_Block_Adminhtml_Report_Customer_Segment_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('segment_id');
        $this->getMassactionBlock()->addItem('view', array(
            'label' => __('View Combined Report'),
            'url'  => $this->getUrl('*/*/detail', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                     'name'     => 'view_mode',
                     'type'     => 'select',
                     'class'    => 'required-entry',
                     'label'    => __('Set'),
                     'values'   => $this->_customerSegmentData->getOptionsArray()
                )
            )
        ));
        return $this;
    }

    /**
     * Retrieve row click URL
     *
     * @param Magento_Object $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/detail', array('segment_id' => $row->getId()));
    }
}
