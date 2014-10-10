<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Category\Attribute;

/**
 * @todo Remove this interface
 */
interface OptionManagementInterface
{
    /**
     * Retrieve list of attribute options
     *
     * @param string $attributeId
     * @return  \Magento\Eav\Api\Data\Entity\Attribute\OptionInterface[]
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     *
     * @todo maybe, get rid of this interface because AttributeMetadataInterface has getOptions() method.
     * @see \Magento\Catalog\Service\V1\Category\Attribute\ReadServiceInterface::options- previous implementation
     */
    public function getList($attributeId);
}
