<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml billing agreement grid container
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Block_Adminhtml_Billing_Agreement extends Magento_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Initialize billing agreements grid container
     *
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_billing_agreement';
        $this->_blockGroup = 'Magento_Sales';
        $this->_headerText = __('Billing Agreements');
        parent::_construct();
        $this->_removeButton('add');
    }
}
