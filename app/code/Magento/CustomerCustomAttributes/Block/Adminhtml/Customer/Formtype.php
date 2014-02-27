<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerCustomAttributes\Block\Adminhtml\Customer;

/**
 * Form Types Grid Container Block
 *
 * @category   Magento
 * @package    Magento_CustomerCustomAttributes
 */
class Formtype extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Block constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_CustomerCustomAttributes';
        $this->_controller = 'adminhtml_customer_formtype';
        $this->_headerText = __('Manage Form Types');

        parent::_construct();

        $this->_updateButton('add', 'label', __('New Form Type'));
    }
}
