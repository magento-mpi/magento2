<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api\Entity\Attribute;

/**
 * Interface MetadataRepositoryInterface
 * @TODO: implement abstract class in EAV module, that will use entity type and override it for category / product entities catalog module
 * @see \Magento\Catalog\Service\V1\MetadataService
 */
interface MetadataRepositoryInterface 
{
    /**
     * @param $attributeCode
     * @return \Magento\Eav\Api\Data\Entity\Attribute\MetadataInterface
     */
    public function get($attributeCode);

    /**
     * @param $searchCriteria
     * @return \Magento\Eav\Api\Data\Entity\Attribute\MetadataInterface[]
     */
    public function getList($searchCriteria);
}
