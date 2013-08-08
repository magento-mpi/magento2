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
 * Generate options for media storage selection
 */
class Mage_Backend_Model_Config_Source_Storage_Media_Storage implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Magento_Core_Model_File_Storage::STORAGE_MEDIA_FILE_SYSTEM,
                'label' => Mage::helper('Mage_Backend_Helper_Data')->__('File System')
            ),
            array(
                'value' => Magento_Core_Model_File_Storage::STORAGE_MEDIA_DATABASE,
                'label' => Mage::helper('Mage_Backend_Helper_Data')->__('Database')
            )
        );
    }

}
