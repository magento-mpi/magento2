<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute;

/**
 * Class WriteServiceInterface
 * @package Magento\Catalog\Service\V1\Product\Attribute
 */
interface WriteServiceInterface
{
    /**
     * Update product attribute process
     *
     * @param  string $id
     * @param  \Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata $attribute
     * @return string
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function update($id, \Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata $attribute);
}
