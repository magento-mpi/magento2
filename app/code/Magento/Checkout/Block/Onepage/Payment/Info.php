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
 * Multishipping checkout payment information data
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Block_Onepage_Payment_Info extends Magento_Payment_Block_Info_ContainerAbstract
{
    /**
     * Retrieve payment info model
     *
     * @return Magento_Payment_Model_Info
     */
    public function getPaymentInfo()
    {
        $info = Mage::getSingleton('Magento_Checkout_Model_Session')->getQuote()->getPayment();
        if ($info->getMethod()) {
            return $info;
        }
        return false;
    }

    protected function _toHtml()
    {
        $html = '';
        if ($block = $this->getChildBlock($this->_getInfoBlockName())) {
            $html = $block->toHtml();
        }
        return $html;
    }
}
