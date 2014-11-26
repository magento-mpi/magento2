<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute\Media;

/**
 * @deprecated
 */
interface ReadServiceInterface
{
    /**
     * Return all media attributes for pointed attribute set
     *
     * @param int $attributeSetId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @return \Magento\Catalog\Service\V1\Product\Attribute\Media\Data\MediaImage[]
     * @see \Magento\Catalog\Api\ProductMediaAttributeManagementInterface::getList
     */
    public function types($attributeSetId);

    /**
     * @param string $productSku
     * @return \Magento\Catalog\Service\V1\Product\Attribute\Media\Data\GalleryEntry[]
     * @see \Magento\Catalog\Api\ProductAttributeMediaGalleryManagementInterface::getList
     */
    public function getList($productSku);

    /**
     * Return information about gallery entity
     *
     * @param string $productSku
     * @param int $imageId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return \Magento\Catalog\Service\V1\Product\Attribute\Media\Data\GalleryEntry
     * @see \Magento\Catalog\Api\ProductAttributeMediaGalleryManagementInterface::get
     */
    public function info($productSku, $imageId);
}
