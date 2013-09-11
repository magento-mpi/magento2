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
 * Customer address attributes Grid Container
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Address;

class Attribute
    extends \Magento\Adminhtml\Block\Widget\Grid\Container
{
    /**
     * Define controller, block and labels
     *
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_CustomerCustomAttributes';
        $this->_controller = 'adminhtml_customer_address_attribute';
        $this->_headerText = __('Customer Address Attributes');
        $this->_addButtonLabel = __('Add New Attribute');
        parent::_construct();
    }
}
