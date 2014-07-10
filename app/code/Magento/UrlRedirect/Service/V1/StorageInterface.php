<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRedirect\Service\V1;

use \Magento\UrlRedirect\Service\V1\Storage\Data\Filter;

/**
 * Url Storage Interface
 */
interface StorageInterface
{
    /**
     * Save url rewrites. Return number of saved urls
     *
     * @param Storage\Data\AbstractData[] $urls
     * @return int
     */
    public function save(array $urls);

    /**
     * Match provided request path for store and if matched - return corresponding Data Object
     *
     * @param string $requestPath
     * @param int $storeId
     * @return \Magento\UrlRedirect\Service\V1\Storage\Data\AbstractData
     */
    public function match($requestPath, $storeId);

    /**
     * Match provided entity for store and if matched - return corresponding Data Object
     *
     * @param int $entityId
     * @param int $entityType
     * @param int $storeId
     * @return \Magento\UrlRedirect\Service\V1\Storage\Data\AbstractData
     */
    public function matchByEntity($entityId, $entityType, $storeId);

    /**
     * Find row by specific filter
     *
     * @param \Magento\UrlRedirect\Service\V1\Storage\Data\Filter $filter
     * @return \Magento\Framework\Service\Data\AbstractObject|null
     */
    public function findByFilter(Filter $filter);

    /**
     * Find rows by specific filter
     *
     * @param \Magento\UrlRedirect\Service\V1\Storage\Data\Filter $filter
     * @return \Magento\Framework\Service\Data\AbstractObject[]
     */
    public function findAllByFilter(Filter $filter);
}
