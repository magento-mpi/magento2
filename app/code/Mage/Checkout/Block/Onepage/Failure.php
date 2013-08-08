<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Checkout_Block_Onepage_Failure extends Magento_Core_Block_Template
{
    public function getRealOrderId()
    {
        return Mage::getSingleton('Mage_Checkout_Model_Session')->getLastRealOrderId();
    }

    /**
     *  Payment custom error message
     *
     *  @return	  string
     */
    public function getErrorMessage ()
    {
        $error = Mage::getSingleton('Mage_Checkout_Model_Session')->getErrorMessage();
        // Mage::getSingleton('Mage_Checkout_Model_Session')->unsErrorMessage();
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
