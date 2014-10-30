<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Dynamic\Algorithm;

interface AlgorithmInterface
{
    /**
     * @param int[] $entityIds
     * @param int[] $intervals
     * @return mixed
     */
    public function getItems(array $entityIds, array $intervals);
}
 