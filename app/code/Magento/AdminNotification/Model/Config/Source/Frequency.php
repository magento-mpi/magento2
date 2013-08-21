<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminNotification
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * AdminNotification update frequency source
 *
 * @category   Magento
 * @package    Magento_AdminNotification
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdminNotification_Model_Config_Source_Frequency implements Magento_Core_Model_Option_ArrayInterface
{
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
