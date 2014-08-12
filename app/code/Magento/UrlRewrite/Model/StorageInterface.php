<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Model;

// TODO: structure layer knows about service layer(and version) (@TODO: UrlRewrite)
use Magento\UrlRewrite\Service\V1\Data\FilterInterface;

/**
 * Url Storage Interface
 */
interface StorageInterface
{
    /**
     * Find all rows by specific filter
     *
     * @param FilterInterface $filter
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    public function findAllByFilter(FilterInterface $filter);

    /**
     * Find row by specific filter
     *
     * @param FilterInterface $filter
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite
     */
    public function findByFilter(FilterInterface $filter);

    /**
     * Add multiple urls to storage
     *
     * @param array $urls
     * @return void
     * @throws Storage\DuplicateEntryException
     */
    public function addMultiple(array $urls);

    /**
     * Delete data from storage by specific filter
     *
     * @param FilterInterface $filter
     * @return void
     */
    public function deleteByFilter(FilterInterface $filter);
}
