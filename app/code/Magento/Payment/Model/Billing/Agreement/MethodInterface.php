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
interface Magento_Payment_Model_Billing_Agreement_MethodInterface
{
    /**
     * Init billing agreement
     *
     * @param Magento_Payment_Model_Billing_AgreementAbstract $agreement
     */
    public function initBillingAgreementToken(Magento_Payment_Model_Billing_AgreementAbstract $agreement);

    /**
     * Retrieve billing agreement details
     *
     * @param Magento_Payment_Model_Billing_AgreementAbstract $agreement
     */
    public function getBillingAgreementTokenInfo(Magento_Payment_Model_Billing_AgreementAbstract $agreement);

    /**
     * Create billing agreement
     *
     * @param Magento_Payment_Model_Billing_AgreementAbstract $agreement
     */
    public function placeBillingAgreement(Magento_Payment_Model_Billing_AgreementAbstract $agreement);

    /**
     * Update billing agreement status
     *
     * @param Magento_Payment_Model_Billing_AgreementAbstract $agreement
     */
    public function updateBillingAgreementStatus(Magento_Payment_Model_Billing_AgreementAbstract $agreement);
}
