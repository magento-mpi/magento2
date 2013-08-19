<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Settlement reports transaction details
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Block_Adminhtml_Settlement_Details extends Mage_Adminhtml_Block_Widget_Form_Container
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
     * @return Mage_Paypal_Block_Adminhtml_Settlement_Details
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->addChild('form', 'Mage_Paypal_Block_Adminhtml_Settlement_Details_Form');
        return $this;
    }
}
