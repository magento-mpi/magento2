<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
/**
 * Locale timezone source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Source_Locale_Timezone
{
    public function toOptionArray()
    {
        return Mage::app()->getLocale()->getOptionTimezones();
    }
}
