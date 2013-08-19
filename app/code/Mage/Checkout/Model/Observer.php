<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Checkout observer model
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Model_Observer
{
    public function unsetAll()
    {
        Mage::getSingleton('Mage_Checkout_Model_Session')->unsetAll();
    }

    public function loadCustomerQuote()
    {
        try {
            Mage::getSingleton('Mage_Checkout_Model_Session')->loadCustomerQuote();
        }
        catch (Mage_Core_Exception $e) {
            Mage::getSingleton('Mage_Checkout_Model_Session')->addError($e->getMessage());
        }
        catch (Exception $e) {
            Mage::getSingleton('Mage_Checkout_Model_Session')->addException(
                $e,
                __('Load customer quote error')
            );
        }
    }

    public function salesQuoteSaveAfter($observer)
    {
        $quote = $observer->getEvent()->getQuote();
        /* @var $quote Mage_Sales_Model_Quote */
        if ($quote->getIsCheckoutCart()) {
            Mage::getSingleton('Mage_Checkout_Model_Session')->getQuoteId($quote->getId());
        }
    }
}
