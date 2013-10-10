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
namespace Magento\Checkout\Model;

class Observer
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(\Magento\Checkout\Model\Session $checkoutSession)
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
        catch (\Magento\Core\Exception $e) {
            $this->_checkoutSession->addError($e->getMessage());
        }
        catch (\Exception $e) {
            $this->_checkoutSession->addException(
                $e,
                __('Load customer quote error')
            );
        }
    }

    public function salesQuoteSaveAfter($observer)
    {
        $quote = $observer->getEvent()->getQuote();
        /* @var $quote \Magento\Sales\Model\Quote */
        if ($quote->getIsCheckoutCart()) {
            $this->_checkoutSession->getQuoteId($quote->getId());
        }
    }
}
