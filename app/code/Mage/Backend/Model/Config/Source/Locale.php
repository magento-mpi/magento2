<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
/**
 * Locale source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Source_Locale implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return Mage::app()->getLocale()->getOptionLocales();
    }
}
