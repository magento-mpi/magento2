<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Layer\Filter\Dynamic;

interface AlgorithmInterface
{
    /**
     * @param int[] $intervals
     * @param string $additionalRequestData
     * @return array
     */
    public function getItemsData(array $intervals = [], $additionalRequestData = '');
}
