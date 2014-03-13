<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Block\Adminhtml;

/**
 * Adminhtml sales orders block
 */
class Payment extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Instructions to create child grid
     *
     * @var string
     */
    protected $_blockGroup = 'Magento_RecurringPayment';

    /**
     * @var string
     */
    protected $_controller = 'adminhtml_payment';

    /**
     * Set header text and remove "add" btn
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_headerText = __('Recurring Billing Payments (beta)');
        parent::_construct();
        $this->_removeButton('add');
    }
}
