<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales orders block
 */
namespace Magento\RecurringPayment\Block\Adminhtml;

class Payment extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Instructions to create child grid
     *
     * @var string
     */
    protected $_blockGroup = 'Magento_RecurringPayment';
    protected $_controller = 'adminhtml_payment';

    /**
     * Set header text and remove "add" btn
     */
    protected function _construct()
    {
        $this->_headerText = __('Recurring Billing Payments (beta)');
        parent::_construct();
        $this->_removeButton('add');
    }
}
