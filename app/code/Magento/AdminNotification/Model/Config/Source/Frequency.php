<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdminNotification\Model\Config\Source;

/**
 * AdminNotification update frequency source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Frequency implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            1 => __('1 Hour'),
            2 => __('2 Hours'),
            6 => __('6 Hours'),
            12 => __('12 Hours'),
            24 => __('24 Hours')
        );
    }
}
