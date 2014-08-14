<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Service\V1\Data\Wrapping;

use Magento\GiftWrapping\Service\V1\Data\Wrapping\Image as WrappingImage;

/**
 * @codeCoverageIgnore
 */
class ImageBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * @param string $value
     */
    public function setBase64Content($value)
    {
        $this->_set(WrappingImage::BASE64_CONTENT, $value);
    }

    /**
     * @param string $value
     */
    public function setFileName($value)
    {
        $this->_set(WrappingImage::FILE_NAME, $value);
    }
}
