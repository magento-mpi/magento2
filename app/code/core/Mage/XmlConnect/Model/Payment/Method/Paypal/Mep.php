<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 * PayPal Mobile Embedded Payments Checkout Module
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Payment_Method_Paypal_Mep extends Mage_Paypal_Model_Express
{
    /**
     * Store MEP payment method code
     */
    const MEP_METHOD_CODE = 'paypal_mep';

    protected $_code  = self::MEP_METHOD_CODE;

    protected $_canUseInternal          = false;
    protected $_canUseForMultishipping  = false;
    protected $_isInitializeNeeded      = false;
    protected $_canUseCheckout          = false;
    protected $_canManageRecurringProfiles = false;

    /**
     * Get config peyment action url
     * Used to universalize payment actions when processing payment place
     *
     * @return string
     */
    public function getConfigPaymentAction()
    {
        return Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE;
    }

    /**
     * Check whether payment method can be used
     * @param Mage_Sales_Model_Quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        return Mage::getModel('paypal/config')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->isMethodAvailable(Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS);
    }

    /**
     * Capture payment
     *
     * @param   Varien_Object $orderPayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function capture(Varien_Object $payment, $amount)
    {
        $transactionId = $payment->getAdditionalInformation(Mage_XmlConnect_Model_Paypal_Mep_Checkout::PAYMENT_INFO_TRANSACTION_ID);
        $payment->setTransactionId($transactionId);
        return $this;
    }

    /**
     * Retrieve information from payment configuration
     *
     * @param   string $field
     * @return  mixed
     */
    public function getConfigData($field, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->getStore();
        }
        switch ($field)
        {
            case 'allowspecific':
            case 'specificcountry':
            case 'line_items_enabled':
            case 'business_account':
                $path = 'paypal/general/' . $field;
            default:
                $path = 'payment/'.$this->getCode().'/'.$field;
        }

        return Mage::getStoreConfig($path, $storeId);
    }
}
