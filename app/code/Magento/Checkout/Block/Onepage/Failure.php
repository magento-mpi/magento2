<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Checkout\Block\Onepage;

class Failure extends \Magento\Core\Block\Template
{
    public function getRealOrderId()
    {
        return \Mage::getSingleton('Magento\Checkout\Model\Session')->getLastRealOrderId();
    }

    /**
     *  Payment custom error message
     *
     *  @return	  string
     */
    public function getErrorMessage ()
    {
        $error = \Mage::getSingleton('Magento\Checkout\Model\Session')->getErrorMessage();
        // \Mage::getSingleton('Magento\Checkout\Model\Session')->unsErrorMessage();
        return $error;
    }

    /**
     * Continue shopping URL
     *
     *  @return	  string
     */
    public function getContinueShoppingUrl()
    {
        return \Mage::getUrl('checkout/cart');
    }
}
