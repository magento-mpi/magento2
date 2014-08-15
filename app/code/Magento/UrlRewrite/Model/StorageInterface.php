<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Model;

use Magento\UrlRewrite\Service\V1\Data\Filter;

interface StorageInterface
{
    /**
     * Find all rows by specific filter
     *
     * @param Filter $filter
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    public function findAllByFilter(Filter $filter);

    /**
     * Find row by specific filter
     *
     * @param Filter $filter
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite
     */
    public function findByFilter(Filter $filter);

    /**
     * Add multiple urls to storage
     *
     * @param \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[] $urls
     * @return void
     * @throws Storage\DuplicateEntryException
     */
    public function addMultiple(array $urls);

    /**
     * Delete data from storage by specific filter
     *
     * @param Filter $filter
     * @return void
     */
    public function deleteByFilter(Filter $filter);
}
