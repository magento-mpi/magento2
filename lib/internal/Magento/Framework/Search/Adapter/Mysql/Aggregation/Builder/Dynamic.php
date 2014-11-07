<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder;

use Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderInterface;
use Magento\Framework\Search\Dynamic\Algorithm\Repository;
use Magento\Framework\Search\Request\Aggregation\DynamicBucket;
use Magento\Framework\Search\Request\BucketInterface as RequestBucketInterface;

class Dynamic implements BucketInterface
{
    /**
     * @var Repository
     */
    private $algorithmRepository;

    /**
     * @param Repository $algorithmRepository
     */
    public function __construct(Repository $algorithmRepository)
    {
        $this->algorithmRepository = $algorithmRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function build(
        DataProviderInterface $dataProvider,
        array $dimensions,
        RequestBucketInterface $bucket,
        array $entityIds
    ) {
        /** @var DynamicBucket $bucket */
        $algorithm = $this->algorithmRepository->get($bucket->getMethod());
        $data = $algorithm->getItems($bucket, $dimensions, $entityIds);

        $resultData = $this->prepareData($data);

        return $resultData;
    }

    /**
     * Prepare result data
     *
     * @param array $data
     * @return array
     */
    private function prepareData($data)
    {
        $resultData = [];
        foreach ($data as $value) {
            $from = $value['from'] ?: '*';
            $to = $value['to'] ?: '*';
            unset($value['from'], $value['to']);

            $rangeName = "{$from}_{$to}";
            $resultData[$rangeName] = array_merge(['value' => $rangeName], $value);
        }

        return $resultData;
    }
}
