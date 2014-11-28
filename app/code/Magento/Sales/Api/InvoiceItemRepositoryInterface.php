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
interface InvoiceItemRepositoryInterface
{
    /**
     * @param \Magento\Framework\Api\SearchCriteria $criteria
     * @return \Magento\Sales\Api\Data\InvoiceItemSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $criteria);

    /**
     * Load entity
     *
     * @param int $id
     * @return \Magento\Sales\Api\Data\InvoiceItemInterface
     */
    public function get($id);

    /**
     * Delete entity
     *
     * @param \Magento\Sales\Api\Data\InvoiceItemInterface $entity
     * @return bool
     */
    public function delete(\Magento\Sales\Api\Data\InvoiceItemInterface $entity);

    /**
     * Perform persist operations for one entity
     *
     * @param \Magento\Sales\Api\Data\InvoiceItemInterface $entity
     * @return \Magento\Sales\Api\Data\InvoiceItemInterface
     */
    public function save(\Magento\Sales\Api\Data\InvoiceItemInterface $entity);
}
