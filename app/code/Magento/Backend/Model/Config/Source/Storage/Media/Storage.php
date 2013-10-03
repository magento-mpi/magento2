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
namespace Magento\Backend\Model\Config\Source\Storage\Media;

class Storage implements \Magento\Core\Model\Option\ArrayInterface
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
                'value' => \Magento\Core\Model\File\Storage::STORAGE_MEDIA_FILE_SYSTEM,
                'label' => __('File System')
            ),
            array(
                'value' => \Magento\Core\Model\File\Storage::STORAGE_MEDIA_DATABASE,
                'label' => __('Database')
            )
        );
    }

}
