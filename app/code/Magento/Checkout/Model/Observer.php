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
 * Checkout observer model
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Model_Observer
{
    public function unsetAll()
    {
        Mage::getSingleton('Magento_Checkout_Model_Session')->unsetAll();
    }

    public function loadCustomerQuote()
    {
        try {
            Mage::getSingleton('Magento_Checkout_Model_Session')->loadCustomerQuote();
        }
        catch (Magento_Core_Exception $e) {
            Mage::getSingleton('Magento_Checkout_Model_Session')->addError($e->getMessage());
        }
        catch (Exception $e) {
            Mage::getSingleton('Magento_Checkout_Model_Session')->addException(
                $e,
                __('Load customer quote error')
            );
        }
    }

    public function salesQuoteSaveAfter($observer)
    {
        $quote = $observer->getEvent()->getQuote();
        /* @var $quote Magento_Sales_Model_Quote */
        if ($quote->getIsCheckoutCart()) {
            Mage::getSingleton('Magento_Checkout_Model_Session')->getQuoteId($quote->getId());
        }
    }
}
