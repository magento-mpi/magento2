<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Cms\Api;

/**
 * Interface PageRepositoryInterface
 */
interface PageRepositoryInterface
{
    /**
     * Save Page data
     *
     * @param \Magento\Cms\Api\Data\PageInterface $page
     * @return \Magento\Cms\Api\Data\PageInterface
     */
    public function save(\Magento\Cms\Api\Data\PageInterface $page);

    /**
     * Load Page data by given Page Identity
     *
     * @param string $pageId
     * @return \Magento\Cms\Api\Data\PageInterface
     */
    public function get($pageId);

    /**
     * Load Page data collection by given search criteria
     *
     * @param \Magento\Cms\Api\PageCriteriaInterface $criteria
     * @return \Magento\Cms\Api\Data\PageCollectionInterface
     */
    public function getList(\Magento\Cms\Api\PageCriteriaInterface $criteria);

    /**
     * Delete Page
     *
     * @param \Magento\Cms\Api\Data\PageInterface $page
     * @return bool
     */
    public function delete(\Magento\Cms\Api\Data\PageInterface $page);

    /**
     * Delete Page by given Page Identity
     *
     * @param string $pageId
     * @return bool
     */
    public function deleteById($pageId);
}
