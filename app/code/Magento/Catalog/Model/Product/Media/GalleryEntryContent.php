<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product\Media;

class GalleryEntryContent extends \Magento\Framework\Model\AbstractExtensibleModel implements
    \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryContentInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEntryData()
    {
        return $this->getData(self::DATA);
    }

    /**
     * {@inheritdoc}
     */
    public function getMimeType()
    {
        return $this->getData(self::MIME_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }
}
