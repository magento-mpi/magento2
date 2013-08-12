<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Checkout_Block_Onepage_Failure extends Magento_Core_Block_Template
{
    public function getRealOrderId()
    {
        return Mage::getSingleton('Magento_Checkout_Model_Session')->getLastRealOrderId();
    }

    /**
     *  Payment custom error message
     *
     *  @return	  string
     */
    public function getErrorMessage ()
    {
        $error = Mage::getSingleton('Magento_Checkout_Model_Session')->getErrorMessage();
        // Mage::getSingleton('Magento_Checkout_Model_Session')->unsErrorMessage();
        return $error;
    }

    /**
     * Continue shopping URL
     *
     *  @return	  string
     */
    public function getContinueShoppingUrl()
    {
        return Mage::getUrl('checkout/cart');
    }
}
