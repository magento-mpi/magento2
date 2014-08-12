<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Service\V1\Data\Filter;

use Magento\UrlRewrite\Service\V1\Data\FilterInterface as UrlRewriteFilterInterface;
use Magento\UrlRewrite\Service\V1\Data\IdentityInterface;

class DefaultFilter implements IdentityInterface, UrlRewriteFilterInterface
{
    protected $data = [];

    public function __construct(
        $possibleFields = [],
        $filterData = []
    ) {
        if ($filterData && $possibleFields) {
            $wrongFields = array_diff(array_keys($filterData), $possibleFields);
            if ($wrongFields) {
                throw new \InvalidArgumentException(
                    sprintf('There is wrong fields passed to filter: "%s"', implode(', ', $wrongFields))
                );
            }
            $this->data = $filterData;
        }
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

    public function getFilter()
    {
        return $this->data;
    }

    public function getFilterType()
    {
        return $this->data['entity_type'];
    }

    /**
     * @param int $entityId
     *
     * @return $this
     */
    public function setEntityId($entityId)
    {
        return $this->_set('entity_id', $entityId);
    }

    /**
     * @param int|array $entityType
     *
     * @return $this
     */
    public function setEntityType($entityType)
    {
        return $this->_set('entity_type', $entityType);
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->_set('store_id', $storeId);
    }
}
