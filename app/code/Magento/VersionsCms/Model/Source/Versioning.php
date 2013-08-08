<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Versioning configuration source model
 *
 * @category   Magento
 * @package    Magento_VersionsCms
 */
class Magento_VersionsCms_Model_Source_Versioning
{
    /**
     * Retrieve options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            '1' => Mage::helper('Magento_VersionsCms_Helper_Data')->__('Enabled by Default'),
            '1' => Mage::helper('Magento_VersionsCms_Helper_Data')->__('Disabled by Default')
        );
    }
}
