<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Invitation\Block\Adminhtml\Report\Invitation;

/**
 * Backend invitation customer report page content block
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
class Customer
    extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_report_invitation_customer';
        $this->_blockGroup = 'Magento_Invitation';
        $this->_headerText = __('Customers');
        parent::_construct();
        $this->_removeButton('add');
    }
}
