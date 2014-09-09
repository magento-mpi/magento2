<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search;

/**
 * Search Request Builder
 */
class RequestBuilder
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    private $objectManager;

    /**
     * @var Request\Config
     */
    private $config;

    /**
     * @var array
     */
    private $data = [
        'dimensions' => [],
        'queries' => [],
        'filters' => []
    ];

    /**
     * Request Builder constructor
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param \Magento\Framework\Search\Request\Config $config
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        \Magento\Framework\Search\Request\Config $config
    ) {
        $this->objectManager = $objectManager;
        $this->config = $config;
    }

    /**
     * Set request name
     *
     * @param string $requestName
     * @return $this
     */
    public function setRequestName($requestName)
    {
        $this->data['requestName'] = $requestName;
        return $this;
    }

    /**
     * Set size
     *
     * @param int $size
     * @return $this
     */
    public function setSize($size)
    {
        $this->data['size'] = $size;
        return $this;
    }

    /**
     * Set from
     *
     * @param int $from
     * @return $this
     */
    public function setFrom($from)
    {
        $this->data['from'] = $from;
        return $this;
    }

    /**
     * Bind dimension data by name
     *
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function bindDimension($name, $value)
    {
        $this->data['dimensions'][$name] = $value;
        return $this;
    }

    /**
     * Bind query data by name
     *
     * @param string $name
     * @param string|int|float $value
     * @return $this
     */
    public function bindQuery($name, $value)
    {
        $this->data['queries'][$name] = $value;
        return $this;
    }

    /**
     * Bind filter data by name
     *
     * @param string $name
     * @param string|int|float $value
     * @return $this
     */
    public function bindFilter($name, $value)
    {
        if (!is_array($value)) {
            $value = ['value' => $value];
        }
        $this->data['filters'][$name] = $value;
        return $this;
    }

    /**
     * Create request object
     *
     * @return RequestInterface
     */
    public function create()
    {
        $requestName = $this->getRequestName();
        /** @var array $data */
        $data = $this->config->get($requestName);
        if (is_null($data)) {
            throw new \InvalidArgumentException("Request name '{$requestName}' doesn't exist.");
        }
        $replacedData = $this->replaceBinds($data, $this->data);
        $this->clear();

        return $this->convert($replacedData);
    }

    /**
     * Get request name
     *
     * @return string
     */
    public function getRequestName()
    {
        return $this->data['requestName'];
    }

    /**
     * Replace binds
     *
     * @param array $data
     * @param array $bindData
     * @return array
     */
    private function replaceBinds($data, $bindData)
    {
        $data = $this->replaceBindLimits($data, $bindData);
        $data['dimensions'] = $this->replaceBindDimensions($data['dimensions'], $bindData['dimensions']);
        $data['queries'] = $this->replaceBindMatchQuery($data['queries'], $bindData['queries']);
        $data['filters'] = $this->replaceBindFilters($data['filters'], $bindData['filters']);

        return $data;
    }

    /**
     * Replace bind limits
     *
     * @param array $data
     * @param array $bindData
     * @return array
     */
    private function replaceBindLimits($data, $bindData)
    {
        $limitList = ['from', 'size'];
        foreach ($limitList as $limit) {
            if (isset($bindData[$limit])) {
                $data[$limit] = $bindData[$limit];
            }
        }
        return $data;
    }

    /**
     * @param array $data
     * @param array $bindData
     * @return array
     */
    private function replaceBindDimensions($data, $bindData)
    {
        foreach ($data as $name => $value) {
            if (isset($bindData[$name])) {
                $data[$name]['value'] = $bindData[$name];
            }
        }
        return $data;
    }

    /**
     * Replace bind match query
     *
     * @param array $queries
     * @param array $bindData
     * @return array
     */
    private function replaceBindMatchQuery($queries, $bindData)
    {
        foreach ($queries as $queryName => $queryValue) {
            if (isset($bindData[$queryName])) {
                $queries[$queryName]['value'] = $bindData[$queryName];
            }
        }
        return $queries;
    }

    /**
     * Replace bind filters
     *
     * @param array $filters
     * @param array $bindDataList
     * @return array
     */
    private function replaceBindFilters($filters, $bindDataList)
    {
        foreach ($bindDataList as $bindFilterName => $bindFilterValue) {
            foreach ($bindFilterValue as $bindFieldName => $bindFieldValue) {
                if (isset($filters[$bindFilterName])) {
                    $filters[$bindFilterName][$bindFieldName] = $bindFieldValue;
                }
            }
        }
        return $filters;
    }

    /**
     * Clear data
     *
     * @return void
     */
    private function clear()
    {
        $this->data = [];
    }

    /**
     * Convert array to Request instance
     *
     * @param array $data
     * @return RequestInterface
     */
    private function convert($data)
    {
        /** @var \Magento\Framework\Search\Request\Mapper $mapper */
        $mapper = $this->objectManager->create(
            'Magento\Framework\Search\Request\Mapper',
            [
                'objectManager' => $this->objectManager,
                'rootQueryName' => $data['query'],
                'queries' => $data['queries'],
                'aggregations' => $data['aggregations'],
                'filters' => $data['filters']
            ]
        );
        return $this->objectManager->create(
            'Magento\Framework\Search\Request',
            [
                'name' => $data['query'],
                'indexName' => $data['index'],
                'from' => $data['from'],
                'size' => $data['size'],
                'query' => $mapper->getRootQuery(),
                'dimensions' => array_map(
                    function ($data) {
                        return $this->objectManager->create(
                            'Magento\Framework\Search\Request\Dimension',
                            $data
                        );
                    },
                    isset($data['dimensions']) ? $data['dimensions'] : []
                ),
                'buckets' => $mapper->getBuckets()
            ]
        );
    }
}
