<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Payment\Model\Recurring\Profile;

/**
 * Recurring profile gateway management interface
 */
interface MethodInterface
{
    /**
     * Validate data
     *
     * @param \Magento\RecurringProfile\Model\RecurringProfile $profile
     * @return void
     * @throws \Magento\Core\Exception
     */
    public function validateRecurringProfile(\Magento\RecurringProfile\Model\RecurringProfile $profile);

    /**
     * Submit to the gateway
     *
     * @param \Magento\RecurringProfile\Model\RecurringProfile $profile
     * @param \Magento\Payment\Model\Info $paymentInfo
     * @return void
     */
    public function submitRecurringProfile(\Magento\RecurringProfile\Model\RecurringProfile $profile, \Magento\Payment\Model\Info $paymentInfo);

    /**
     * Fetch details
     *
     * @param string $referenceId
     * @param \Magento\Object $result
     * @return void
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
     * @param \Magento\RecurringProfile\Model\RecurringProfile $profile
     * @return void
     */
    public function updateRecurringProfile(\Magento\RecurringProfile\Model\RecurringProfile $profile);

    /**
     * Manage status
     *
     * @param \Magento\RecurringProfile\Model\RecurringProfile $profile
     * @return void
     */
    public function updateRecurringProfileStatus(\Magento\RecurringProfile\Model\RecurringProfile $profile);
}
