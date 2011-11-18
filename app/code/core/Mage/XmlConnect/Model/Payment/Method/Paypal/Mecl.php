<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * XmlConnect PayPal Mobile Express Checkout Library model
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Payment_Method_Paypal_Mecl extends Mage_Paypal_Model_Express
{
    /**
     * Store MECL payment method code
     */
    const MECL_METHOD_CODE = 'paypal_mecl';

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = self::MECL_METHOD_CODE;

    /**
     * Can use method for a frontend checkout
     *
     * @var bool
     */
    protected $_canUseCheckout = false;

    /**
     * Can method be used for multishipping checkout type
     *
     * @var bool
     */
    protected $_canUseForMultishipping = false;

    /**
     * Can method manage recurring profiles
     *
     * @var bool
     */
    protected $_canManageRecurringProfiles = false;

    /**
     * Check whether payment method can be used
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        $storeId = false;
        $model = Mage::registry('current_app');

        if ($model instanceof Mage_XmlConnect_Model_Application) {
            $storeId = $model->getStoreId();
        }

        if (!$storeId) {
            $storeId = $quote ? $quote->getStoreId() : Mage::app()->getStore()->getId();
        }

        return (bool) Mage::getModel('Mage_Paypal_Model_Config')->setStoreId($storeId)
            ->isMethodAvailable(Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS);
    }

    /**
     * Return title of the PayPal Mobile Express Checkout Payment method
     *
     * @return string
     */
    public function getTitle()
    {
        return Mage::helper('Mage_XmlConnect_Helper_Data')->__('PayPal Mobile Express Checkout');
    }
}
