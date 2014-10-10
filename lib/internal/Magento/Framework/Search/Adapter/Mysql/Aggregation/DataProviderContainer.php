<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Aggregation;

class DataProviderContainer
{
    /**
     * @var DataProviderInterface[]
     */
    private $dataProvider;

    /**
     * @param DataProviderInterface[] $dataProviders
     */
    public function __construct(array $dataProviders)
    {
        $this->dataProvider = $dataProviders;
    }

    /**
     * @param string $indexName
     * @return DataProviderInterface
     */
    public function get($indexName)
    {
        return $this->dataProvider[$indexName];
    }
}
