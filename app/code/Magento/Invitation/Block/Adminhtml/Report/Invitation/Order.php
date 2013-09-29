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
 * Backend invitation order report page content block
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
namespace Magento\Invitation\Block\Adminhtml\Report\Invitation;

class Order extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_report_invitation_order';
        $this->_blockGroup = 'Magento_Invitation';
        $this->_headerText = __('Order Conversion Rate');
        parent::_construct();
        $this->_removeButton('add');
    }
}
