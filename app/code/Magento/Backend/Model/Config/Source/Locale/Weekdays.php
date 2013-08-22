<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Locale timezone source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Model_Config_Source_Locale_Weekdays implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return Mage::app()->getLocale()->getOptionWeekdays();
    }
}
