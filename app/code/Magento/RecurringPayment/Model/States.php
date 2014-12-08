<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Model;

class States implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var string
     */
    const UNKNOWN = 'unknown';

    const PENDING = 'pending';

    const ACTIVE = 'active';

    const SUSPENDED = 'suspended';

    const CANCELED = 'canceled';

    const EXPIRED = 'expired';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            self::UNKNOWN => __('Not Initialized'),
            self::PENDING => __('Pending'),
            self::ACTIVE => __('Active'),
            self::SUSPENDED => __('Suspended'),
            self::CANCELED => __('Canceled'),
            self::EXPIRED => __('Expired')
        ];
    }
}
