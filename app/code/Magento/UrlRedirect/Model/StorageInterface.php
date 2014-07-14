<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRedirect\Model;

use Magento\UrlRedirect\Model\Data\Filter;

/**
 * Url Storage Interface
 */
interface StorageInterface
{
    /**
     * Find all rows by specific filter
     *
     * @param Filter $filter
     * @return array
     */
    public function findAllByFilter(Filter $filter);

    /**
     * Find row by specific filter
     *
     * @param Filter $filter
     * @return array
     */
    public function findByFilter(Filter $filter);

    /**
     * Add multiple data to storage
     *
     * @param array $data
     */
    public function add(array $data);

    /**
     * Delete data from storage by specific filter
     *
     * @param Filter $filter
     */
    public function deleteByFilter(Filter $filter);
}
