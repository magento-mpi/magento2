<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Backend_Model_Config_Source_Image_Adapter implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * Return hash of image adapter codes and labels
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Mage_Core_Model_Image_AdapterFactory::ADAPTER_IM  =>
                __('ImageMagick'),
            Mage_Core_Model_Image_AdapterFactory::ADAPTER_GD2 =>
                __('PHP GD2'),
        );
    }
}
