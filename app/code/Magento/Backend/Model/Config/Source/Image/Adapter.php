<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Backend\Model\Config\Source\Image;

class Adapter implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Return hash of image adapter codes and labels
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            \Magento\Image\Adapter\AdapterInterface::ADAPTER_IM  =>
                __('ImageMagick'),
            \Magento\Image\Adapter\AdapterInterface::ADAPTER_GD2 =>
                __('PHP GD2'),
        );
    }
}
