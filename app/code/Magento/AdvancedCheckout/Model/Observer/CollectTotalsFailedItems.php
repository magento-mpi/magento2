<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Model\Observer;

use \Magento\AdvancedCheckout\Model\Cart;

class CollectTotalsFailedItems
{
    /**
     * @var Cart
     */
    protected $_cart;

    /**
     * @var \Magento\AdvancedCheckout\Model\FailedItemProcessor
     */
    protected $failedItemProcessor;

    /**
     * @param Cart $cart
     * @param \Magento\AdvancedCheckout\Model\FailedItemProcessor $failedItemProcessor
     */
    public function __construct(
        Cart $cart,
        \Magento\AdvancedCheckout\Model\FailedItemProcessor $failedItemProcessor
    ) {
        $this->_cart = $cart;
        $this->failedItemProcessor = $failedItemProcessor;
    }

    /**
     * Calculate failed items quote-related data
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute($observer)
    {
        if ($observer->getEvent()->getFullActionName() != 'checkout_cart_index') {
            return;
        }

        $affectedItems = $this->_cart->getFailedItems();
        if (empty($affectedItems)) {
            return;
        }
        $this->failedItemProcessor->process();
    }
}
