<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Block\Adminhtml;

/**
 * Admin tax rule content block
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Agreement extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_agreement';
        $this->_blockGroup = 'Magento_Checkout';
        $this->_headerText = __('Terms and Conditions');
        $this->_addButtonLabel = __('Add New Condition');
        parent::_construct();
    }
}
