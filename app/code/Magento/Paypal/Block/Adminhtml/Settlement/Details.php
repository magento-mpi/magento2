<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Settlement reports transaction details
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Paypal_Block_Adminhtml_Settlement_Details extends Magento_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Block construction
     * Initialize titles, buttons
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_controller = '';
        $this->_headerText = __('View Transaction Details');
        $this->_removeButton('reset')
            ->_removeButton('delete')
            ->_removeButton('save');
    }

    /**
     * Initialize form
     * @return Magento_Paypal_Block_Adminhtml_Settlement_Details
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->addChild('form', 'Magento_Paypal_Block_Adminhtml_Settlement_Details_Form');
        return $this;
    }
}
