<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Service\V1\Data;

use Magento\GiftWrapping\Service\V1\Data\Wrapping as WrappingData;

/**
 * @codeCoverageIgnore
 */
class WrappingBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * @param int
     */
    public function setWrappingId($id)
    {
        $this->_set(WrappingData::WRAPPING_ID, $id);
    }

    /**
     * @param string $design
     */
    public function setDesign($design)
    {
        $this->_set(WrappingData::DESIGN, $design);
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->_set(WrappingData::STATUS, $status);
    }

    /**
     * @param float $price
     */
    public function setBasePrice($price)
    {
        $this->_set(WrappingData::BASE_PRICE, $price);
    }

    /**
     * @param string $name
     */
    public function setImageName($name)
    {
        $this->_set(WrappingData::IMAGE_NAME, $name);
    }

    /**
     * @param string $content
     */
    public function setImageBase64Content($content)
    {
        $this->_set(WrappingData::IMAGE_BASE64_CONTENT, $content);
    }

    /**
     * @param string
     */
    public function setImageUrl($url)
    {
        $this->_set(WrappingData::IMAGE_URL, $url);
    }

    /**
     * @param int[] $ids
     */
    public function setWebsiteIds($ids)
    {
        $this->_set(WrappingData::WEBSITE_IDS, $ids);
    }
}
