<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Service\Entity;

class TestService
{
    /**
     * @param int $entityId
     * @param string $name
     * @return string[]
     */
    public function simple($entityId, $name)
    {
        return [$entityId, $name];
    }

    /**
     * @param \Magento\Webapi\Service\Entity\NestedDto $nested
     * @return \Magento\Webapi\Service\Entity\NestedDto
     */
    public function nestedDto(NestedDto $nested)
    {
        return $nested;
    }

    /**
     * @param int[] $ids
     * @return int[]
     */
    public function simpleArray(array $ids)
    {
        return $ids;
    }

    /**
     * @param string[] $associativeArray
     * @return string[]
     */
    public function associativeArray(array $associativeArray)
    {
        return $associativeArray;
    }

    /**
     * @param \Magento\Webapi\Service\Entity\SimpleDto[] $dtos
     * @return \Magento\Webapi\Service\Entity\SimpleDto[]
     */
    public function dtoArray(array $dtos)
    {
        return $dtos;
    }

    /**
     * @param \Magento\Webapi\Service\Entity\SimpleArrayDto $arrayDto
     * @return \Magento\Webapi\Service\Entity\SimpleArrayDto
     */
    public function nestedSimpleArray(SimpleArrayDto $arrayDto)
    {
        return $arrayDto;
    }

    /**
     * @param \Magento\Webapi\Service\Entity\AssociativeArrayDto $associativeArrayDto
     * @return \Magento\Webapi\Service\Entity\AssociativeArrayDto
     */
    public function nestedAssociativeArray(AssociativeArrayDto $associativeArrayDto)
    {
        return $associativeArrayDto;
    }

    /**
     * @param \Magento\Webapi\Service\Entity\DtoArrayDto $dtos
     * @return \Magento\Webapi\Service\Entity\DtoArrayDto
     */
    public function nestedDtoArray(DtoArrayDto $dtos)
    {
        return $dtos;
    }
}
