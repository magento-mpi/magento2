<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Model\Method\Checks;

use Magento\Payment\Model\Checks\PaymentMethodChecksInterface;
use Magento\Paypal\Model\Config;
use Magento\Sales\Model\Quote;
use Magento\Payment\Model\Checks\SpecificationInterface;
use Magento\Paypal\Model\Billing\AgreementFactory;

class SpecificationPlugin
{
    /**
     * @var AgreementFactory
     */
    protected $_agreementFactory;

    /**
     * @param AgreementFactory $agreementFactory
     */
    public function __construct(AgreementFactory $agreementFactory)
    {
        $this->_agreementFactory = $agreementFactory;
    }

    /**
     * Override check for Billing Agreements
     *
     * @param SpecificationInterface $specification
     * @param \Closure $proceed
     * @param PaymentMethodChecksInterface $paymentMethod
     * @param Quote $quote
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundIsApplicable(
        SpecificationInterface $specification,
        \Closure $proceed,
        PaymentMethodChecksInterface $paymentMethod,
        Quote $quote
    ) {
        $originallyIsApplicable = $proceed($paymentMethod, $quote);
        if (!$originallyIsApplicable) {
            return false;
        }

        if ($paymentMethod->getCode() == Config::METHOD_BILLING_AGREEMENT) {
            if ($quote->getCustomerId()) {
                $availableBA = $this->_agreementFactory->create()->getAvailableCustomerBillingAgreements(
                    $quote->getCustomerId()
                );
                return count($availableBA) > 0;
            }
            return false;
        }

        return true;
    }
}
