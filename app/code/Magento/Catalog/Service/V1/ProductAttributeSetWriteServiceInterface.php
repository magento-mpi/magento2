<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

interface ProductAttributeSetWriteServiceInterface
{
    /**
     * Create attribute set from data
     *
     * @param \Magento\Catalog\Service\V1\Data\Eav\AttributeSet $attributeSet
     * @return int
     * @throws \Exception
     */
    public function create(\Magento\Catalog\Service\V1\Data\Eav\AttributeSet $attributeSet);
}
