<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Model;

// TODO: structure layer knows about service layer(and version) (@TODO: UrlRewrite)
use Magento\UrlRewrite\Service\V1\Data\Filter;

/**
 * Url Storage Interface
 */
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

    /**
     * Find row by specific data
     *
     * @param array $data
     * @return mixed
     */
    public function findByData(array $data);
}
