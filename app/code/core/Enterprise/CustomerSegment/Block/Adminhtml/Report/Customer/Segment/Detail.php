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
 * Customer Segments Detail grid container
 *
 * @category   Enterprise
 * @package    Enterprise_CustomerSegment
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CustomerSegment_Block_Adminhtml_Report_Customer_Segment_Detail
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->_blockGroup = 'Enterprise_CustomerSegment';
        $this->_controller = 'adminhtml_report_customer_segment_detail';
        $this->_headerText = (!$this->escapeHtml($this->getCustomerSegment()->getName()))
            ? Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Customer Segments Report')
            : Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Customer Segment Report \'%s\'',$this->escapeHtml($this->getCustomerSegment()->getName()));

        parent::__construct();
        $this->_removeButton('add');
        $this->addButton('back', array(
            'label'     => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Back'),
            'onclick'   => 'setLocation(\'' . $this->getBackUrl() .'\')',
            'class'     => 'back',
        ));
        $this->addButton('refresh', array(
            'label'     => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Refresh Segment Data'),
            'onclick'   => 'setLocation(\'' . $this->getRefreshUrl() .'\')',
        ));
    }

    /**
     * Get URL for refresh button
     *
     * @return string
     */
    public function getRefreshUrl()
    {
        return $this->getUrl('*/*/refresh', array('_current' => true));
    }

    /**
     * Get URL for back button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/segment');
    }

    /**
     * Getter
     *
     * @return Enterprise_CustomerSegment_Model_Segment
     */
    public function getCustomerSegment()
    {
        return Mage::registry('current_customer_segment');
    }

    /**
     * Retrieve all websites
     *
     * @return array
     */
    public function getWebsites()
    {
        return Mage::app()->getWebsites();
    }

}
