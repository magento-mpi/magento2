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
interface CreditmemoItemRepositoryInterface
{
    /**
     * Load entity
     *
     * @param int $id
     * @return \Magento\Sales\Api\Data\CreditmemoItemInterface
     */
    public function get($id);

    /**
     * @param \Magento\Framework\Api\SearchCriteria $criteria
     * @return \Magento\Sales\Api\Data\CreditmemoItemSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $criteria);

    /**
     * Delete entity
     *
     * @param \Magento\Sales\Api\Data\CreditmemoInterface $entity
     * @return bool
     */
    public function delete(\Magento\Sales\Api\Data\CreditmemoInterface $entity);

    /**
     * Perform persist operations for one entity
     *
     * @param \Magento\Sales\Api\Data\CreditmemoInterface $entity
     * @return \Magento\Sales\Api\Data\CreditmemoInterface
     */
    public function save(\Magento\Sales\Api\Data\CreditmemoInterface $entity);
}
