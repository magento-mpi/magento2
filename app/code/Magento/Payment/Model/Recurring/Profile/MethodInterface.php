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
namespace Magento\Payment\Model\Recurring\Profile;

interface MethodInterface
{
    /**
     * Validate data
     *
     * @param \Magento\Payment\Model\Recurring\Profile $profile
     * @throws \Magento\Core\Exception
     */
    public function validateRecurringProfile(\Magento\Payment\Model\Recurring\Profile $profile);

    /**
     * Submit to the gateway
     *
     * @param \Magento\Payment\Model\Recurring\Profile $profile
     * @param \Magento\Payment\Model\Info $paymentInfo
     */
    public function submitRecurringProfile(\Magento\Payment\Model\Recurring\Profile $profile, \Magento\Payment\Model\Info $paymentInfo);

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
     * @param \Magento\Payment\Model\Recurring\Profile $profile
     */
    public function updateRecurringProfile(\Magento\Payment\Model\Recurring\Profile $profile);

    /**
     * Manage status
     *
     * @param \Magento\Payment\Model\Recurring\Profile $profile
     */
    public function updateRecurringProfileStatus(\Magento\Payment\Model\Recurring\Profile $profile);
}
