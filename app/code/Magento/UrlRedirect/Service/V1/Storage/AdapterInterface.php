<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRedirect\Service\V1\Storage;

/**
 * Storage Adapter interface
 */
interface AdapterInterface
{
    /**
     * Find all rows by specific filter
     *
     * @param \Magento\UrlRedirect\Service\V1\Storage\Data\Filter $filter
     * @return array
     */
    public function findAll(Data\Filter $filter);

    /**
     * Find row by specific filter
     *
     * @param \Magento\UrlRedirect\Service\V1\Storage\Data\Filter $filter
     * @return array
     */
    public function find(Data\Filter $filter);

    /**
     * Add multiple data to storage
     *
     * @param array $data Column-value pairs or array of Column-value pairs.
     * @return int
     */
    public function add(array $data);

    /**
     * Delete data from storage by specific filter
     *
     * @param \Magento\UrlRedirect\Service\V1\Storage\Data\Filter $filter
     * @return mixed
     */
    public function delete(Data\Filter $filter);
}
