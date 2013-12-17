<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml customers list block
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Customer\Block\Adminhtml;

class Customer extends \Magento\Backend\Block\Widget\Grid\Container
{

    protected function _construct()
    {
        $this->_controller = 'adminhtml';
        $this->_blockGroup = 'Magento_Customer';
        $this->_headerText = __('Customers');
        $this->_addButtonLabel = __('Add New Customer');
        parent::_construct();
    }

}
