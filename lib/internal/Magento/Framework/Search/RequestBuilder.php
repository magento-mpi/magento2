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
     * @var array
     */
    private $data = [];

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
     * @param string $requestName
     * @return $this
     */
    public function setRequestName($requestName)
    {
        $this->data['requestName'] = $requestName;
        return $this;
    }

    /**
     * @param int $size
     * @return $this
     */
    public function setSize($size)
    {
        $this->data['size'] = $size;
        return $this;
    }

    /**
     * @param int $from
     * @return $this
     */
    public function setFrom($from)
    {
        $this->data['from'] = $from;
        return $this;
    }

    /**
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
        return $replacedData;
    }

    /**
     * @return string
     */
    public function getRequestName()
    {
        return $this->data['requestName'];
    }

    private function replaceBinds($data, $bindData)
    {
        $data = $this->replaceBindLimits($data, $bindData);
        $data['dimensions'] = $this->replaceBindDimensions($data['dimensions'], $bindData['dimensions']);
        $data['queries'] = $this->replaceBindMatchQuery($data['queries'], $bindData['queries']);
        $data['filters'] = $this->replaceBindFilters($data['filters'], $bindData['filters']);

        return $data;
    }

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

    private function replaceBindDimensions($data, $bindData)
    {
        foreach ($data as $name => $value) {
            if (isset($bindData[$name])) {
                $data[$name]['value'] = $bindData[$name];
            }
        }
        return $data;
    }

    private function replaceBindMatchQuery($queries, $bindData)
    {
        foreach ($queries as $queryName => $queryValue) {
            if (isset($bindData[$queryName])) {
                foreach ($queryValue['match'] as $matchKey => $matchValue) {
                    $queries[$queryName]['match'][$matchKey]['value'] = $bindData[$queryName];
                }
            }
        }
        return $queries;
    }

    private function replaceBindFilters($filters, $bindData)
    {
        foreach ($filters as $filterName => $filterValue) {
            foreach ($filterValue as $fieldName => $fieldValue) {
                if (isset($bindData[$filterName][$fieldName])) {
                    $filters[$filterName][$fieldName] = $bindData[$filterName][$fieldName];
                }
            }
        }
        return $filters;
    }

    private function clear()
    {
        $this->data = [];
    }
}
