<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdminNotification\Model\Config\Source;

/**
 * AdminNotification update frequency source
 *
 * @category   Magento
 * @package    Magento_AdminNotification
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Frequency implements \Magento\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            1   => __('1 Hour'),
            2   => __('2 Hours'),
            6   => __('6 Hours'),
            12  => __('12 Hours'),
            24  => __('24 Hours')
        );
    }
}
