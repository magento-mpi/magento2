<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Edit order address form container block
 */
class Mage_Adminhtml_Block_Sales_Order_Address extends Mage_Adminhtml_Block_Widget_Form_Container
{

    protected function _construct()
    {
        $this->_controller = 'sales_order';
        $this->_mode       = 'address';
        parent::_construct();
        $this->_updateButton('save', 'label', Mage::helper('Mage_Sales_Helper_Data')->__('Save Order Address'));
        $this->_removeButton('delete');
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        $address = Mage::registry('order_address');
        $orderId = $address->getOrder()->getIncrementId();
        if ($address->getAddressType() == 'shipping') {
            $type = Mage::helper('Mage_Sales_Helper_Data')->__('Shipping');
        } else {
            $type = Mage::helper('Mage_Sales_Helper_Data')->__('Billing');
        }
        return Mage::helper('Mage_Sales_Helper_Data')->__('Edit Order %s %s Address', $orderId, $type);
    }

    /**
     * Back button url getter
     *
     * @return string
     */
    public function getBackUrl()
    {
        $address = Mage::registry('order_address');
        return $this->getUrl(
            '*/*/view',
            array('order_id' => $address ? $address->getOrder()->getId() : null)
        );
    }
}
