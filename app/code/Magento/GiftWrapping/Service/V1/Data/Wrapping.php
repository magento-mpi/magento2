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
class Wrapping extends \Magento\Framework\Service\Data\AbstractObject
{
    /**#@+
     * Data object properties
     * @var string
     */
    const WRAPPING_ID = 'wrapping_id';
    const DESIGN = 'design';
    const STATUS = 'status';
    const BASE_PRICE = 'base_price';
    const IMAGE = 'image';
    const IMAGE_URL = 'image_url';
    const WEBSITE_IDS = 'website_ids';
    /**#@-*/

    /**
     * @return int
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
     * @return \Magento\GiftWrapping\Service\V1\Data\Wrapping\Image
     */
    public function getImage()
    {
        return $this->_get(self::IMAGE);
    }

    /**
     * @return string
     */
    public function getImageUrl()
    {
        return $this->_get(self::IMAGE_URL);
    }

    /**
     * @return int[]
     */
    public function getWebsiteIds()
    {
        return $this->_get(self::WEBSITE_IDS);
    }
}
