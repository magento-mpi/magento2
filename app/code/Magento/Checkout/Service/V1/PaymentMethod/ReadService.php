<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\PaymentMethod;

use \Magento\Checkout\Service\V1\QuoteLoader;
use Magento\Framework\StoreManagerInterface;
use Magento\Checkout\Service\V1\Data\Cart\PaymentMethod\Converter as QuoteMethodConverter;
use Magento\Checkout\Service\V1\Data\PaymentMethod\Converter as PaymentMethodConverter;
use \Magento\Payment\Model\MethodList;
use \Magento\Framework\Exception\State\InvalidTransitionException;

class ReadService implements ReadServiceInterface
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
     * @var QuoteMethodConverter
     */
    protected $quoteMethodConverter;

    /**
     * @var PaymentMethodConverter
     */
    protected $paymentMethodConverter;

    /**
     * @var MethodList
     */
    protected $methodList;

    /**
     * @param QuoteLoader $quoteLoader
     * @param StoreManagerInterface $storeManager
     * @param QuoteMethodConverter $quoteMethodConverter
     * @param PaymentMethodConverter $paymentMethodConverter
     * @param MethodList $methodList
     */
    public function __construct(
        QuoteLoader $quoteLoader,
        StoreManagerInterface $storeManager,
        QuoteMethodConverter $quoteMethodConverter,
        PaymentMethodConverter $paymentMethodConverter,
        MethodList $methodList
    ) {
        $this->storeManager = $storeManager;
        $this->quoteLoader = $quoteLoader;
        $this->quoteMethodConverter = $quoteMethodConverter;
        $this->paymentMethodConverter = $paymentMethodConverter;
        $this->methodList = $methodList;
    }

    /**
     * {@inheritdoc}
     */
    public function getPayment($cartId)
    {
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteLoader->load($cartId, $this->storeManager->getStore()->getId());

        $payment = $quote->getPayment();
        if (!$payment->getId()) {
            return null;
        }
        return $this->quoteMethodConverter->toDataObject($payment);
    }

    /**
     * {@inheritdoc}
     */
    public function getList($cartId)
    {
        $output = [];
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteLoader->load($cartId, $this->storeManager->getStore()->getId());
        foreach ($this->methodList->getAvailableMethods($quote) as $method) {
            $output[] = $this->paymentMethodConverter->toDataObject($method);
        }
        return $output;
    }
}
