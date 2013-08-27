<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Backend_Model_Config_Source_Image_Adapter implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Return hash of image adapter codes and labels
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Magento_Core_Model_Image_AdapterFactory::ADAPTER_IM  =>
                __('ImageMagick'),
            Magento_Core_Model_Image_AdapterFactory::ADAPTER_GD2 =>
                __('PHP GD2'),
        );
    }
}
