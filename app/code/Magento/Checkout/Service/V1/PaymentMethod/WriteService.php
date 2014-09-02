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
use \Magento\Payment\Model\Checks\ZeroTotal;


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
     * @var ZeroTotal
     */
    protected $zeroTotalValidator;

    /**
     * @param QuoteLoader $quoteLoader
     * @param StoreManagerInterface $storeManager
     * @param Builder $paymentMethodBuilder
     * @param ZeroTotal $zeroTotalValidator
     */
    public function __construct(
        QuoteLoader $quoteLoader,
        StoreManagerInterface $storeManager,
        Builder $paymentMethodBuilder,
        ZeroTotal $zeroTotalValidator
    ) {
        $this->storeManager = $storeManager;
        $this->quoteLoader = $quoteLoader;
        $this->paymentMethodBuilder = $paymentMethodBuilder;
        $this->zeroTotalValidator = $zeroTotalValidator;
    }

    /**
     * {@inheritdoc}
     */
    public function set(\Magento\Checkout\Service\V1\Data\Cart\PaymentMethod $method, $cartId)
    {
        $quote = $this->quoteLoader->load($cartId, $this->storeManager->getStore()->getId());

        $payment = $this->paymentMethodBuilder->build($method, $quote);
        if ($quote->isVirtual()) {
            // check if billing address is set
            if (is_null($quote->getBillingAddress()->getCountryId())) {
                throw new InvalidTransitionException('Billing address is not set');
            }
            $quote->getBillingAddress()->setPaymentMethod($payment->getMethod());
        } else {
            // check if shipping address is set
            if (is_null($quote->getShippingAddress()->getCountryId())) {
                throw new InvalidTransitionException('Shipping address is not set');
            }
            $quote->getShippingAddress()->setPaymentMethod($payment->getMethod());
        }
        if (!$quote->isVirtual() && $quote->getShippingAddress()) {
            $quote->getShippingAddress()->setCollectShippingRates(true);
        }

        if (!$this->zeroTotalValidator->isApplicable($payment->getMethodInstance(), $quote)) {
            throw new InvalidTransitionException('The requested Payment Method is not available.');
        }

        $quote->setTotalsCollectedFlag(false)
            ->collectTotals()
            ->save();

        return $quote->getPayment()->getId();
    }
}
