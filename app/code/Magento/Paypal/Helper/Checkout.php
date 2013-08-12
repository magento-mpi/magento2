<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Checkout workflow helper
 */
class Magento_Paypal_Helper_Checkout extends Magento_Core_Helper_Abstract
{
    /**
     * @var Magento_Checkout_Model_SessionFactory
     */
    protected $_session;

    /**
     * @var Magento_Sales_Model_QuoteFactory
     */
    protected $_quoteFactory;

    /**
     * @param Magento_Checkout_Model_Session $session
     * @param Magento_Sales_Model_QuoteFactory $quoteFactory
     */
    public function __construct(
        Magento_Checkout_Model_Session $session,
        Magento_Sales_Model_QuoteFactory $quoteFactory
    ) {
        $this->_session = $session;
        $this->_quoteFactory = $quoteFactory;
    }

    /**
     * Restore last active quote based on checkout session
     *
     * @return bool True if quote restored successfully, false otherwise
     */
    public function restoreQuote()
    {
        $order = $this->_session->getLastRealOrder();
        if ($order->getId()) {
            $quote = $this->_quoteFactory->create()->load($order->getQuoteId());
            if ($quote->getId()) {
                $quote->setIsActive(1)
                    ->setReservedOrderId(null)
                    ->save();
                $this->_session->replaceQuote($quote)
                    ->unsLastRealOrderId();
                return true;
            }
        }
        return false;
    }

    /**
     * Cancel last placed order with specified comment message
     *
     * @param string $comment Comment appended to order history
     * @return bool True if order cancelled, false otherwise
     */
    public function cancelCurrentOrder($comment)
    {
        $order = $this->_session->getLastRealOrder();
        if ($order->getId() && $order->getState() != Magento_Sales_Model_Order::STATE_CANCELED) {
            $order->registerCancellation($comment)->save();
            return true;
        }
        return false;
    }
}
