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
     * Bind data to placeholder
     *
     * @param string $placeholder
     * @param string $value
     * @return $this
     */
    public function bind($placeholder, $value)
    {
        $this->data['placeholder'][$placeholder] = $value;
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
        $data['queries'] = $this->replaceData($data['queries'], $bindData['placeholder']);
        $data['filters'] = $this->replaceData($data['filters'], $bindData['placeholder']);
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
     * Replace data recursive
     *
     * @param array $data
     * @param array $bindData
     * @return array
     */
    private function replaceData($data, $bindData)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->replaceData($value, $bindData);
            } elseif (!empty($bindData[$value])) {
                $data[$key] = $bindData[$value];
            }
        }
        return $data;
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
