<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Form Types Grid Container Block
 *
 * @category   Magento
 * @package    Magento_CustomerCustomAttributes
 */
namespace Magento\CustomerCustomAttributes\Block\Adminhtml\Customer;

class Formtype extends \Magento\Adminhtml\Block\Widget\Grid\Container
{
    /**
     * Block constructor
     *
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
