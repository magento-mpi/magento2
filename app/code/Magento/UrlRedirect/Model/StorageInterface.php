<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRedirect\Model;

// TODO: structure layer knows about service layer(and version) (MAGETWO-25952)
use Magento\UrlRedirect\Service\V1\Data\Filter;

/**
 * Url Storage Interface
 */
interface StorageInterface
{
    /**
     * Find all rows by specific filter
     *
     * @param Filter $filter
     * @return \Magento\UrlRedirect\Service\V1\Data\UrlRewrite[]
     */
    public function findAllByFilter(Filter $filter);

    /**
     * Find row by specific filter
     *
     * @param Filter $filter
     * @return \Magento\UrlRedirect\Service\V1\Data\UrlRewrite
     */
    public function findByFilter(Filter $filter);

    /**
     * Add multiple urls to storage
     *
     * @param array $urls
     * @return void
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
