<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend invitation customer report page content block
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
class Magento_Invitation_Block_Adminhtml_Report_Invitation_Customer
    extends Magento_Backend_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_report_invitation_customer';
        $this->_blockGroup = 'Magento_Invitation';
        $this->_headerText = __('Customers');
        parent::_construct();
        $this->_removeButton('add');
    }
}
