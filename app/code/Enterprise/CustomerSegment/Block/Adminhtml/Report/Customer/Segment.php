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
 * Customer Segments grid container
 *
 * @category   Enterprise
 * @package    Enterprise_CustomerSegment
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CustomerSegment_Block_Adminhtml_Report_Customer_Segment
    extends Mage_Backend_Block_Widget_Grid_Container
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Enterprise_CustomerSegment';
        $this->_controller = 'adminhtml_report_customer_segment';
        $this->_headerText = Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Customer Segment Report');
        parent::_construct();
        $this->_removeButton('add');
    }

}
