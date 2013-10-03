<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for available settlement report fetching intervals
 */
namespace Magento\Paypal\Model\System\Config\Source;

class FetchingSchedule implements \Magento\Core\Model\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array (
            1 => __("Daily"),
            3 => __("Every 3 days"),
            7 => __("Every 7 days"),
            10 => __("Every 10 days"),
            14 => __("Every 14 days"),
            30 => __("Every 30 days"),
            40 => __("Every 40 days"),
        );
    }
}
