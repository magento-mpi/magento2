<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Service\V1\Data;

/**
 * @codeCoverageIgnore
 */
class Wrapping extends \Magento\Framework\Service\Data\AbstractExtensibleObject
{
    /**#@+
     * Data object properties
     * @var string
     */
    const WRAPPING_ID = 'wrapping_id';
    const DESIGN = 'design';
    const STATUS = 'status';
    const BASE_PRICE = 'base_price';
    const IMAGE_NAME = 'image_name';
    const IMAGE_BASE64_CONTENT = 'image_base64_content';
    const IMAGE_URL = 'image_url';
    const WEBSITE_IDS = 'website_ids';
    /**#@-*/

    /**
     * @return int|null
     */
    public function getWrappingId()
    {
        return $this->_get(self::WRAPPING_ID);
    }

    /**
     * @return string
     */
    public function getDesign()
    {
        return $this->_get(self::DESIGN);
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * @return float
     */
    public function getBasePrice()
    {
        return $this->_get(self::BASE_PRICE);
    }

    /**
     * @return string|null
     */
    public function getImageName()
    {
        return $this->_get(self::IMAGE_NAME);
    }

    /**
     * @return string|null
     */
    public function getImageBase64Content()
    {
        return $this->_get(self::IMAGE_BASE64_CONTENT);
    }

    /**
     * @return string|null
     */
    public function getImageUrl()
    {
        return $this->_get(self::IMAGE_URL);
    }

    /**
     * @return int[]|null
     */
    public function getWebsiteIds()
    {
        return $this->_get(self::WEBSITE_IDS);
    }
}
