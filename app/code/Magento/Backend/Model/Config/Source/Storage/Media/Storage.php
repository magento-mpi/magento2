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
 * Generate options for media storage selection
 */
class Magento_Backend_Model_Config_Source_Storage_Media_Storage implements Magento_Core_Model_Option_ArrayInterface
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
                'label' => __('File System')
            ),
            array(
                'value' => Magento_Core_Model_File_Storage::STORAGE_MEDIA_DATABASE,
                'label' => __('Database')
            )
        );
    }

}
