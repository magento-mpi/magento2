<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface for payment methods that support billing agreements management
 */
namespace Magento\Paypal\Model\Billing\Agreement;

interface MethodInterface
{
    /**
     * Init billing agreement
     *
     * @param \Magento\Paypal\Model\Billing\AbstractAgreement $agreement
     */
    public function initBillingAgreementToken(\Magento\Paypal\Model\Billing\AbstractAgreement $agreement);

    /**
     * Retrieve billing agreement details
     *
     * @param \Magento\Paypal\Model\Billing\AbstractAgreement $agreement
     */
    public function getBillingAgreementTokenInfo(\Magento\Paypal\Model\Billing\AbstractAgreement $agreement);

    /**
     * Create billing agreement
     *
     * @param \Magento\Paypal\Model\Billing\AbstractAgreement $agreement
     */
    public function placeBillingAgreement(\Magento\Paypal\Model\Billing\AbstractAgreement $agreement);

    /**
     * Update billing agreement status
     *
     * @param \Magento\Paypal\Model\Billing\AbstractAgreement $agreement
     */
    public function updateBillingAgreementStatus(\Magento\Paypal\Model\Billing\AbstractAgreement $agreement);
}
