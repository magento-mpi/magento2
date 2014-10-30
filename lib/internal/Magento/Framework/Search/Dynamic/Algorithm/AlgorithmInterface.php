<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Dynamic\Algorithm;

use Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderInterface;

interface AlgorithmInterface
{
    /**
     * @param DataProviderInterface $dataProvider
     * @param int[] $entityIds
     * @param int[] $intervals
     * @return mixed
     */
    public function getItems(DataProviderInterface $dataProvider, array $entityIds, array $intervals);
}
 