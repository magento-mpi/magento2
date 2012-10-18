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
 * Generate options for media storage selection
 */
class Mage_Adminhtml_Model_System_Config_Source_Storage_Media_Storage
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
                'value' => Mage_Core_Model_File_Storage::STORAGE_MEDIA_FILE_SYSTEM,
                'label' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('File System')
            ),
            array(
                'value' => Mage_Core_Model_File_Storage::STORAGE_MEDIA_DATABASE,
                'label' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Database')
            )
        );
    }

}
