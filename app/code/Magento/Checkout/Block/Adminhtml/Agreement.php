<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin tax rule content block
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Checkout\Block\Adminhtml;

class Agreement extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_agreement';
        $this->_blockGroup = 'Magento_Checkout';
        $this->_headerText = __('Terms and Conditions');
        $this->_addButtonLabel = __('Add New Condition');
        parent::_construct();
    }
}
