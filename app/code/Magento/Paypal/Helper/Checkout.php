<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Helper;

/**
 * Checkout workflow helper
 */
class Checkout
{
    /**
     * @var \Magento\Checkout\Model\SessionFactory
     */
    protected $_session;

    /**
     * @var \Magento\Sales\Model\QuoteFactory
     */
    protected $_quoteFactory;

    /**
     * @param \Magento\Checkout\Model\Session $session
     * @param \Magento\Sales\Model\QuoteFactory $quoteFactory
     */
    public function __construct(
        \Magento\Checkout\Model\Session $session,
        \Magento\Sales\Model\QuoteFactory $quoteFactory
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
        if ($order->getId() && $order->getState() != \Magento\Sales\Model\Order::STATE_CANCELED) {
            $order->registerCancellation($comment)->save();
            return true;
        }
        return false;
    }
}
