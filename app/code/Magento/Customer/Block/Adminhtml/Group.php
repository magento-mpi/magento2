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
 * Adminhtml customers group page content block
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Block\Adminhtml;

class Group extends \Magento\Backend\Block\Widget\Grid\Container
{

    /**
     * Modify header & button labels
     *
     */
    protected function _construct()
    {
        $this->_controller = 'customer_group';
        $this->_headerText = __('Customer Groups');
        $this->_addButtonLabel = __('Add New Customer Group');
        parent::_construct();
    }

    /**
     * Redefine header css class
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'icon-head head-customer-groups';
    }
}
