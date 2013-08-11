<?php
/**
 * {license_notice}
 *
 * @category    Enterprice
 * @package     Enterprice_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Statuses option array
 *
 * @category   Enterprise
 * @package    Enterprice_Reminder
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reminder_Model_Reminder_Options_Status implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * Return statuses array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            1 => __('Active'),
            0 => __('Inactive'),
        );
    }
}
