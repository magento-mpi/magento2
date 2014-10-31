<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder;

use Magento\Framework\DB\Select;
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
        $data = $algorithm->getItems($dataProvider, $entityIds, []);

        $resultData = [];
        foreach ($data as $value) {
            $from = $value['from'] ?: '*';
            $to = $value['to'] ?: '*';
            unset($value['from'], $value['to']);

            $q = "{$from}_{$to}";
            $resultData[$q] = array_merge(['value' => $q], $value);
        }

        return $resultData;
    }
}
