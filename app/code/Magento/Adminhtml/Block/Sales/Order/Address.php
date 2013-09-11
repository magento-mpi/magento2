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
 * Edit order address form container block
 */
namespace Magento\Adminhtml\Block\Sales\Order;

class Address extends \Magento\Adminhtml\Block\Widget\Form\Container
{

    protected function _construct()
    {
        $this->_controller = 'sales_order';
        $this->_mode       = 'address';
        parent::_construct();
        $this->_updateButton('save', 'label', __('Save Order Address'));
        $this->_removeButton('delete');
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        $address = \Mage::registry('order_address');
        $orderId = $address->getOrder()->getIncrementId();
        if ($address->getAddressType() == 'shipping') {
            $type = __('Shipping');
        } else {
            $type = __('Billing');
        }
        return __('Edit Order %1 %2 Address', $orderId, $type);
    }

    /**
     * Back button url getter
     *
     * @return string
     */
    public function getBackUrl()
    {
        $address = \Mage::registry('order_address');
        return $this->getUrl(
            '*/*/view',
            array('order_id' => $address ? $address->getOrder()->getId() : null)
        );
    }
}
