<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

/**
 * Class ProductAttributeReadServiceInterface
 * @package Magento\Catalog\Service\V1
 */
interface ProductAttributeReadServiceInterface
{
    /**
     * Retrieve list of product attribute types
     *
     * @return \Magento\Catalog\Service\V1\Data\Eav\Product\Attribute\Type[]
     */
    public function types();

    /**
     * Get full information about a required attribute with the list of options
     *
     * @param  string $id
     * @return \Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function info($id);
}
