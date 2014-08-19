<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Service\V1\Data;

class WrappingMapper
{
    /**
     * @var \Magento\GiftWrapping\Service\V1\Data\WrappingBuilder
     */
    protected $wrappingBuilder;

    /**
     * @param \Magento\GiftWrapping\Service\V1\Data\WrappingBuilder $wrappingBuilder
     */
    public function __construct(\Magento\GiftWrapping\Service\V1\Data\WrappingBuilder $wrappingBuilder)
    {
        $this->wrappingBuilder = $wrappingBuilder;
    }

    /**
     * @param \Magento\GiftWrapping\Model\Wrapping $object
     * @return \Magento\GiftWrapping\Service\V1\Data\Wrapping
     */
    public function extractDto(\Magento\GiftWrapping\Model\Wrapping $object)
    {
        $this->wrappingBuilder->populateWithArray($object->getData());
        $this->wrappingBuilder->setWebsiteIds($object->getWebsiteIds());
        $this->wrappingBuilder->setImageName($object->getImage());
        $this->wrappingBuilder->setImageUrl($object->getImageUrl());
        return $this->wrappingBuilder->create();
    }
}
