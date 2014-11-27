<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Api;

/**
 * Interface RepositoryInterface
 */
interface CreditmemoCommentRepositoryInterface
{
    /**
     * Load entity
     *
     * @param int $id
     * @return \Magento\Sales\Api\Data\CreditmemoCommentInterface
     */
    public function get($id);

    /**
     * @param \Magento\Framework\Api\SearchCriteria $criteria
     * @return \Magento\Sales\Api\Data\CreditmemoCommentSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $criteria);

    /**
     * Delete entity
     *
     * @param \Magento\Sales\Api\Data\CreditmemoCommentInterface $entity
     * @return bool
     */
    public function delete(\Magento\Sales\Api\Data\CreditmemoCommentInterface $entity);

    /**
     * Perform persist operations for one entity
     *
     * @param \Magento\Sales\Api\Data\CreditmemoCommentInterface $entity
     * @return \Magento\Sales\Api\Data\CreditmemoCommentInterface
     */
    public function save(\Magento\Sales\Api\Data\CreditmemoCommentInterface $entity);
}
