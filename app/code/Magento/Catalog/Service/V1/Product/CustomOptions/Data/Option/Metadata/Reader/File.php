<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\Reader;

use \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata;

/**
 * @codeCoverageIgnore
 */
class File extends DefaultReader
{
    /**
     * {@inheritdoc}
     */
    protected function getCustomAttributes(\Magento\Catalog\Model\Product\Option $option)
    {
        return [
            Metadata::FILE_EXTENSION => $option->getFileExtension(),
            Metadata::IMAGE_SIZE_X => $option->getImageSizeX(),
            Metadata::IMAGE_SIZE_Y => $option->getImageSizeY(),
        ];
    }
}
