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
     * @param int $attributeSetId
     * @return \Magento\Catalog\Service\V1\Data\Eav\MediaImage[]
     */
    public function getTypes($attributeSetId);
}