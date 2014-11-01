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
class WrappingBuilder extends \Magento\Framework\Api\AbstractExtensibleObjectBuilder
{
    /**
     * @param int $id
     * @return $this
     */
    public function setWrappingId($id)
    {
        return $this->_set(WrappingData::WRAPPING_ID, $id);
    }

    /**
     * @param string $design
     * @return $this
     */
    public function setDesign($design)
    {
        return $this->_set(WrappingData::DESIGN, $design);
    }

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->_set(WrappingData::STATUS, $status);
    }

    /**
     * @param float $price
     * @return $this
     */
    public function setBasePrice($price)
    {
        return $this->_set(WrappingData::BASE_PRICE, $price);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setImageName($name)
    {
        return $this->_set(WrappingData::IMAGE_NAME, $name);
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setImageBase64Content($content)
    {
        return $this->_set(WrappingData::IMAGE_BASE64_CONTENT, $content);
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setImageUrl($url)
    {
        return $this->_set(WrappingData::IMAGE_URL, $url);
    }

    /**
     * @param int[] $ids
     * @return $this
     */
    public function setWebsiteIds($ids)
    {
        return $this->_set(WrappingData::WEBSITE_IDS, $ids);
    }
}
