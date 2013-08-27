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
 * Backend invitation customer report page content block
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Block_Adminhtml_Report_Invitation_Customer
    extends Magento_Backend_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_report_invitation_customer';
        $this->_blockGroup = 'Enterprise_Invitation';
        $this->_headerText = __('Customers');
        parent::_construct();
        $this->_removeButton('add');
    }
}
