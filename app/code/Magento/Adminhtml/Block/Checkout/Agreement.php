<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin tax rule content block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Checkout;

class Agreement extends \Magento\Adminhtml\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller      = 'checkout_agreement';
        $this->_headerText      = __('Terms and Conditions');
        $this->_addButtonLabel  = __('Add New Condition');
        parent::_construct();
    }
}
