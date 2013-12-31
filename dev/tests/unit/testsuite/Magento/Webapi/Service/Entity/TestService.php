<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

/**
 * Class TestService
 *
 * Used to help test DTO serialization
 *
 * @package Magento\Webapi\Service\Entity
 */
class TestService
{
    /**
     * @param int    $entityId
     * @param string $name
     *
     * @return array
     */
    public function simple($entityId, $name)
    {
        return [$entityId, $name];
    }

    /**
     * @param \Magento\Webapi\Service\Entity\NestedDto $nested
     *
     * @return NestedDto
     */
    public function nestedDto(NestedDto $nested)
    {
        return $nested;
    }

    /**
     * @param array $ids
     *
     * @return array
     */
    public function simpleArray(array $ids)
    {
        return $ids;
    }

    /**
     * @param array $associativeArray
     *
     * @return array
     */
    public function associativeArray(array $associativeArray)
    {
        return $associativeArray;
    }

    /**
     * @param \Magento\Webapi\Service\Entity\SimpleDto[] $dtos
     *
     * @return array
     */
    public function dtoArray(array $dtos)
    {
        return $dtos;
    }

    /**
     * @param \Magento\Webapi\Service\Entity\SimpleArrayDto $arrayDto
     *
     * @return SimpleArrayDto
     */
    public function nestedSimpleArray(SimpleArrayDto $arrayDto)
    {
        return $arrayDto;
    }

    /**
     * @param \Magento\Webapi\Service\Entity\AssociativeArrayDto $associativeArrayDto
     *
     * @return AssociativeArrayDto
     */
    public function nestedAssociativeArray(AssociativeArrayDto $associativeArrayDto)
    {
        return $associativeArrayDto;
    }

    /**
     * @param \Magento\Webapi\Service\Entity\DtoArrayDto $dtos
     *
     * @return DtoArrayDto
     */
    public function nestedDtoArray(DtoArrayDto $dtos)
    {
        return $dtos;
    }

}
