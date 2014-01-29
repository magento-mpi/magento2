<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringProfile\Model;

use Magento\Core\Model\Option\ArrayInterface;

class States implements ArrayInterface
{
    const STATE_UNKNOWN = 'unknown';
    const STATE_PENDING = 'pending';
    const STATE_ACTIVE = 'active';
    const STATE_SUSPENDED = 'suspended';
    const STATE_CANCELED = 'canceled';
    const STATE_EXPIRED = 'expired';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            self::STATE_UNKNOWN => __('Not Initialized'),
            self::STATE_PENDING => __('Pending'),
            self::STATE_ACTIVE => __('Active'),
            self::STATE_SUSPENDED => __('Suspended'),
            self::STATE_CANCELED => __('Canceled'),
            self::STATE_EXPIRED => __('Expired'),
        );
    }
}
