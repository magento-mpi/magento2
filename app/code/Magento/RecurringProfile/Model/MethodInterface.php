<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringProfile\Model;

use \Magento\Payment\Model\Info as PaymentInfo;
use \Magento\Object;

/**
 * Recurring profile gateway management interface
 */
interface MethodInterface
{
    /**
     * Validate data
     *
     * @param RecurringProfile $profile
     * @throws \Magento\Core\Exception
     */
    public function validate(RecurringProfile $profile);

    /**
     * Submit to the gateway
     *
     * @param RecurringProfile $profile
     * @param PaymentInfo $paymentInfo
     */
    public function submit(RecurringProfile $profile, PaymentInfo $paymentInfo);

    /**
     * Fetch details
     *
     * @param string $referenceId
     * @param \Magento\Object $result
     */
    public function getDetails($referenceId, Object $result);

    /**
     * Check whether can get recurring profile details
     *
     * @return bool
     */
    public function canGetDetails();

    /**
     * Update data
     *
     * @param RecurringProfile $profile
     */
    public function update(RecurringProfile $profile);

    /**
     * Manage status
     *
     * @param RecurringProfile $profile
     */
    public function updateStatus(RecurringProfile $profile);
}
