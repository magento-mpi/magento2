<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml invitation order report page content block
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Block_Adminhtml_Report_Invitation_Order extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_report_invitation_order';
        $this->_blockGroup = 'Enterprise_Invitation';
        $this->_headerText = Mage::helper('Enterprise_Invitation_Helper_Data')->__('Order Conversion Rate');
        parent::__construct();
        $this->_removeButton('add');
    }
}
