<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Service\V1;

use Magento\UrlRewrite\Service\V1\Data\FilterInterface;

/**
 * Url Matcher Interface
 */
interface UrlMatcherInterface
{
//    /**
//     * Match provided request path for store and if matched - return corresponding Data Object
//     *
//     * @param string $requestPath
//     * @param int $storeId
//     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite|null
//     */
//    public function match($requestPath, $storeId);

//    /**
//     * Match provided entity for store and if matched - return corresponding Data Object
//     *
//     * @param int $entityId
//     * @param int $entityType
//     * @param int $storeId
//     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite|null
//     */
//    public function findByEntity($entityId, $entityType, $storeId);

    /**
     * Find row by specific filter
     *
     * @param FilterInterface $filter
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite|null
     */
    public function findByFilter(FilterInterface $filter);

    /**
     * Find rows by specific filter
     *
     * @param FilterInterface $filter
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    public function findAllByFilter(FilterInterface $filter);
}
