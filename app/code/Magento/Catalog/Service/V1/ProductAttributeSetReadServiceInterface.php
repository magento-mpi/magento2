<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

interface ProductAttributeSetReadServiceInterface
{
    /**
     * @return \Magento\Catalog\Service\V1\Data\Eav\AttributeSet[]
     */
    public function getList();

    /**
     * Retrieve attribute set information based on given ID
     *
     * @param int $attributeSetId
     * @throws \Magento\Framework\Exception\NoSuchEntityException If $attributeSetId is not found
     * @return \Magento\Catalog\Service\V1\Data\Eav\AttributeSet
     */
    public function getInfo($attributeSetId);
}

