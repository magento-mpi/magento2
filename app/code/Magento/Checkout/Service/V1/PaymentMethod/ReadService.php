<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\PaymentMethod;

use \Magento\Sales\Model\QuoteRepository;
use \Magento\Framework\StoreManagerInterface;
use Magento\Checkout\Service\V1\Data\Cart\PaymentMethod\Converter as QuoteMethodConverter;
use Magento\Checkout\Service\V1\Data\PaymentMethod\Converter as PaymentMethodConverter;
use \Magento\Payment\Model\MethodList;

class ReadService implements ReadServiceInterface
{
    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;

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
     * @param QuoteRepository $quoteRepository
     * @param QuoteMethodConverter $quoteMethodConverter
     * @param PaymentMethodConverter $paymentMethodConverter
     * @param MethodList $methodList
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        QuoteMethodConverter $quoteMethodConverter,
        PaymentMethodConverter $paymentMethodConverter,
        MethodList $methodList
    ) {
        $this->quoteRepository = $quoteRepository;
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
        $quote = $this->quoteRepository->get($cartId);
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
        $quote = $this->quoteRepository->get($cartId);
        foreach ($this->methodList->getAvailableMethods($quote) as $method) {
            $output[] = $this->paymentMethodConverter->toDataObject($method);
        }
        return $output;
    }
}
