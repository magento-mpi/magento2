<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\PaymentMethod;

use \Magento\Checkout\Service\V1\QuoteLoader;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Checkout\Service\V1\Data\Cart\PaymentMethod\Builder;
use \Magento\Framework\Exception\State\InvalidTransitionException;
use \Magento\Payment\Model\MethodList;

class WriteService implements WriteServiceInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var QuoteLoader
     */
    protected $quoteLoader;

    /**
     * @var Builder
     */
    protected $paymentMethodBuilder;

    /**
     * @var MethodList
     */
    protected $methodList;

    /**
     * @param QuoteLoader $quoteLoader
     * @param StoreManagerInterface $storeManager
     * @param Builder $paymentMethodBuilder
     */
    public function __construct(
        QuoteLoader $quoteLoader,
        StoreManagerInterface $storeManager,
        Builder $paymentMethodBuilder,
        MethodList $methodList
    ) {
        $this->storeManager = $storeManager;
        $this->quoteLoader = $quoteLoader;
        $this->paymentMethodBuilder = $paymentMethodBuilder;
        $this->methodList = $methodList;
    }

    /**
     * {@inheritdoc}
     */
    public function set(\Magento\Checkout\Service\V1\Data\Cart\PaymentMethod $method, $cartId)
    {
        $quote = $this->quoteLoader->load($cartId, $this->storeManager->getStore()->getId());
        if ($quote->getPaymentsCollection()->getSize()) {
            return null;
        }
        $payment = $this->paymentMethodBuilder->build($method, $quote);
        if ($quote->isVirtual()) {
            // check if billing address is set
            if (is_null($quote->getBillingAddress()->getId())) {
                throw new InvalidTransitionException('Billing address is not set');
            }
            $quote->getBillingAddress()->setPaymentMethod($payment->getMethod());
        } else {
            // check if shipping address is set
            if (is_null($quote->getShippingAddress()->getId())) {
                throw new InvalidTransitionException('Shipping address is not set');
            }
            $quote->getShippingAddress()->setPaymentMethod($payment->getMethod());
        }

        if (!$quote->isVirtual() && $quote->getShippingAddress()) {
            $quote->getShippingAddress()->setCollectShippingRates(true);
        }

        $quote->setTotalsCollectedFlag(false)
            ->collectTotals()
            ->save();

        return $quote->getPayment()->getId();
    }
}
