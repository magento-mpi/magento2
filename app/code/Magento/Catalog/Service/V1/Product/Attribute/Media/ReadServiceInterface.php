<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute\Media;

interface ReadServiceInterface
{
    /**
     * Return all media attributes for pointed attribute set
     *
     * @param int $attributeSetId
     * @return \Magento\Catalog\Service\V1\Product\Attribute\Media\Data\MediaImage[]
     */
    public function getTypes($attributeSetId);
}