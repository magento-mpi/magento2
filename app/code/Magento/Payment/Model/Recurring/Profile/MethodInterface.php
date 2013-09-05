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
 * Recurring profile gateway management interface
 */
interface Magento_Payment_Model_Recurring_Profile_MethodInterface
{
    /**
     * Validate data
     *
     * @param Magento_Payment_Model_Recurring_Profile $profile
     * @throws Magento_Core_Exception
     */
    public function validateRecurringProfile(Magento_Payment_Model_Recurring_Profile $profile);

    /**
     * Submit to the gateway
     *
     * @param Magento_Payment_Model_Recurring_Profile $profile
     * @param Magento_Payment_Model_Info $paymentInfo
     */
    public function submitRecurringProfile(Magento_Payment_Model_Recurring_Profile $profile, Magento_Payment_Model_Info $paymentInfo);

    /**
     * Fetch details
     *
     * @param string $referenceId
     * @param \Magento\Object $result
     */
    public function getRecurringProfileDetails($referenceId, \Magento\Object $result);

    /**
     * Check whether can get recurring profile details
     *
     * @return bool
     */
    public function canGetRecurringProfileDetails();

    /**
     * Update data
     *
     * @param Magento_Payment_Model_Recurring_Profile $profile
     */
    public function updateRecurringProfile(Magento_Payment_Model_Recurring_Profile $profile);

    /**
     * Manage status
     *
     * @param Magento_Payment_Model_Recurring_Profile $profile
     */
    public function updateRecurringProfileStatus(Magento_Payment_Model_Recurring_Profile $profile);
}
