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
 * Backend invitation general report page content block
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
namespace Magento\Invitation\Block\Adminhtml\Report\Invitation;

class General
    extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_report_invitation_general';
        $this->_blockGroup = 'Magento_Invitation';
        $this->_headerText = __('General');
        parent::_construct();
        $this->_removeButton('add');
    }
}
