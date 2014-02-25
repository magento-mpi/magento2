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
     * @param \Magento\Webapi\Service\Entity\NestedData $nested
     * @return \Magento\Webapi\Service\Entity\NestedData
     */
    public function nestedDto(NestedData $nested)
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
     * @param \Magento\Webapi\Service\Entity\SimpleData[] $dataObjects
     * @return \Magento\Webapi\Service\Entity\SimpleData[]
     */
    public function dataArray(array $dataObjects)
    {
        return $dataObjects;
    }

    /**
     * @param \Magento\Webapi\Service\Entity\SimpleArrayData $arrayData
     * @return \Magento\Webapi\Service\Entity\SimpleArrayData
     */
    public function nestedSimpleArray(SimpleArrayData $arrayData)
    {
        return $arrayData;
    }

    /**
     * @param \Magento\Webapi\Service\Entity\AssociativeArrayData $associativeArrayData
     * @return \Magento\Webapi\Service\Entity\AssociativeArrayData
     */
    public function nestedAssociativeArray(AssociativeArrayData $associativeArrayData)
    {
        return $associativeArrayData;
    }

    /**
     * @param \Magento\Webapi\Service\Entity\DataArrayData $dataObjects
     * @return \Magento\Webapi\Service\Entity\DtoArrayDto
     */
    public function nestedDataArray(DataArrayData $dataObjects)
    {
        return $dataObjects;
    }
}
