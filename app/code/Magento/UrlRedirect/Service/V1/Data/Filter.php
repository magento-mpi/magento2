<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRedirect\Service\V1\Data;

/**
 * Url rewrite search filter
 */
class Filter
{
    /**
     * Data with filter values
     *
     * @var array
     */
    protected $data = [];

    /**
     * Possible fields for filter
     *
     * @var array
     */
    protected $possibleFields = [
        UrlRewrite::ENTITY_ID,
        UrlRewrite::ENTITY_TYPE,
        UrlRewrite::STORE_ID,
        UrlRewrite::REQUEST_PATH,
        UrlRewrite::REDIRECT_TYPE,
    ];

    /**
     * Filter constructor
     *
     * @param array $filterData
     * @throws \InvalidArgumentException
     */
    public function __construct(array $filterData = [])
    {
        if ($filterData) {
            if ($wrongFields = array_diff(array_keys($filterData), $this->possibleFields)) {
                throw new \InvalidArgumentException(
                    sprintf('There is wrong fields passed to filter: "%s"', implode(', ', $wrongFields))
                );
            }
            $this->data = $filterData;
        }
        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    protected function _set($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function getFilter()
    {
        return $this->data;
    }

    /**
     * @param int $entityId
     *
     * @return $this
     */
    public function setEntityId($entityId)
    {
        return $this->_set(UrlRewrite::ENTITY_ID, $entityId);
    }

    /**
     * @param int|array $entityType
     *
     * @return $this
     */
    public function setEntityType($entityType)
    {
        return $this->_set(UrlRewrite::ENTITY_TYPE, $entityType);
    }

    /**
     * @param string $requestPath
     *
     * @return $this
     */
    public function setRequestPath($requestPath)
    {
        return $this->_set(UrlRewrite::REQUEST_PATH, $requestPath);
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->_set(UrlRewrite::STORE_ID, $storeId);
    }

    /**
     * @param string|array $redirectType
     *
     * @return $this
     */
    public function setRedirectType($redirectType)
    {
        return $this->_set(UrlRewrite::REDIRECT_TYPE, $redirectType);
    }
}
