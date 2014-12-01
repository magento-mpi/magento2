<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Dynamic\Algorithm;

use Magento\Framework\Search\Request\BucketInterface;

interface AlgorithmInterface
{
    /**
     * @param BucketInterface $bucket
     * @param array $dimensions
     * @param int[] $entityIds
     * @return mixed
     */
    public function getItems(BucketInterface $bucket, array $dimensions, array $entityIds);
}
