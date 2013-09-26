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
    /**
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * @param Magento_Checkout_Model_Session $checkoutSession
     */
    public function __construct(Magento_Checkout_Model_Session $checkoutSession)
    {
        $this->_checkoutSession = $checkoutSession;
    }

    public function unsetAll()
    {
        $this->_checkoutSession->unsetAll();
    }

    public function loadCustomerQuote()
    {
        try {
            $this->_checkoutSession->loadCustomerQuote();
        }
        catch (Magento_Core_Exception $e) {
            $this->_checkoutSession->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_checkoutSession->addException(
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
            $this->_checkoutSession->getQuoteId($quote->getId());
        }
    }
}
