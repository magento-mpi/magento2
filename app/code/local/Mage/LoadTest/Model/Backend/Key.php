<?php
/**
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * LoadTest authorization key backend model
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_LoadTest_Model_Backend_Key extends Mage_Core_Model_Config_Data {
    public function afterSave(Varien_Object $configData)
    {
        $key = $configData->getValue();
        if (empty($key)) {
            Mage::throwException(Mage::helper('Mage_LoadTest_Helper_Data')->__('Authorization key cannot be empty.'));
        }
    }
}