<?php
/**
 * Search Request Pool
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search;

class RequestFactory
{
    const CACHE_PREFIX = 'search_request::';

    /**
     * @var \Magento\Framework\Config\CacheInterface
     */
    private $cache;

    /**
     * @var Request\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\ObjectManager
     */
    private $objectManager;

    /**
     * Request Pool constructor
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param \Magento\Framework\Search\Request\Config $config
     * @param \Magento\Framework\Config\CacheInterface $cache
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        \Magento\Framework\Search\Request\Config $config,
        \Magento\Framework\Config\CacheInterface $cache
    ) {
        $this->objectManager = $objectManager;
        $this->config = $config;
        $this->cache = $cache;
    }

    /**
     * Create Request instance with specified parameters
     *
     * @param string $requestName
     * @param array $bindValues
     * @return \Magento\Framework\Search\Request
     * @throws \InvalidArgumentException
     */
    public function create($requestName, array $bindValues = array())
    {
        $data = $this->config->get($requestName);
        if (is_null($data)) {
            throw new \InvalidArgumentException("Request name '{$requestName}' doesn't exist.");
        }
        $data = $this->replaceBinds((array)$data, array_keys($bindValues), array_values($bindValues));
        return $this->convert($data);
    }

    /**
     * @param string|array $data
     * @param string[] $bindKeys
     * @param string[] $bindValues
     * @return string|array
     */
    private function replaceBinds($data, $bindKeys, $bindValues)
    {
        if (is_scalar($data)) {
            return str_replace($bindKeys, $bindValues, $data);
        } else {
            foreach ($data as $key => $value) {
                $data[$key] = $this->replaceBinds($value, $bindKeys, $bindValues);
            }
            return $data;
        }
    }

    /**
     * Convert array to Request instance
     *
     * @param array $data
     * @return \Magento\Framework\Search\Request
     */
    private function convert($data)
    {
        $mapper = $this->objectManager->create(
            'Magento\Framework\Search\Request\Mapper',
            [
                'objectManager' => $this->objectManager,
                'queries' => $data['queries'],
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
                'query' => $mapper->get($data['query']),
                'buckets' => [],
            ]
        );
    }

}
