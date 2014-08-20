<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Model;

interface StorageInterface
{
    /**
     * Find all rows by specific filter
     *
     * @param array $data
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    public function findAllByData(array $data);

    /**
     * Find row by specific filter
     *
     * @param array $data
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite|null
     */
    public function findOneByData(array $data);

    /**
     * Save new url rewrites and remove old if exist
     *
     * @param \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[] $urls
     * @return void
     */
    public function replace(array $urls);

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
     * @param array $data
     * @return void
     */
    public function deleteByData(array $data);
}
