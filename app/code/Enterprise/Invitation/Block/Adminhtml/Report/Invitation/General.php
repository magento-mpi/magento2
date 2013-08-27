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
 * Backend invitation general report page content block
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Block_Adminhtml_Report_Invitation_General
    extends Magento_Backend_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_report_invitation_general';
        $this->_blockGroup = 'Enterprise_Invitation';
        $this->_headerText = __('General');
        parent::_construct();
        $this->_removeButton('add');
    }
}
