<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue\Reader;

use \Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue;

class File extends DefaultReader
{
    /**
     * {@inheritdoc}
     */
    protected function getCustomAttributes(\Magento\Catalog\Model\Product\Option $option)
    {
        return [
            OptionValue::FILE_EXTENSION => $option->getFileExtension(),
            OptionValue::IMAGE_SIZE_X => $option->getImageSizeX(),
            OptionValue::IMAGE_SIZE_Y => $option->getImageSizeY(),
        ];
    }
}
