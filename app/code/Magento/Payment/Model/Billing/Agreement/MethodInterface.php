<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface for payment methods that support billing agreements management
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Payment\Model\Billing\Agreement;

interface MethodInterface
{
    /**
     * Init billing agreement
     *
     * @param \Magento\Payment\Model\Billing\AgreementAbstract $agreement
     */
    public function initBillingAgreementToken(\Magento\Payment\Model\Billing\AgreementAbstract $agreement);

    /**
     * Retrieve billing agreement details
     *
     * @param \Magento\Payment\Model\Billing\AgreementAbstract $agreement
     */
    public function getBillingAgreementTokenInfo(\Magento\Payment\Model\Billing\AgreementAbstract $agreement);

    /**
     * Create billing agreement
     *
     * @param \Magento\Payment\Model\Billing\AgreementAbstract $agreement
     */
    public function placeBillingAgreement(\Magento\Payment\Model\Billing\AgreementAbstract $agreement);

    /**
     * Update billing agreement status
     *
     * @param \Magento\Payment\Model\Billing\AgreementAbstract $agreement
     */
    public function updateBillingAgreementStatus(\Magento\Payment\Model\Billing\AgreementAbstract $agreement);
}
