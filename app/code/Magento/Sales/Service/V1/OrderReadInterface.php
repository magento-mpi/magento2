<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Framework\Service\V1\Data\SearchCriteria;

interface OrderReadInterface
{
    /**
     * @param int $id
     * @return \Magento\Sales\Service\V1\Data\Order
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id);

    /**
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Sales\Service\V1\Data\OrderSearchResults
     */
    public function search(SearchCriteria $searchCriteria);

    /**
     * @param int $id
     * @return \Magento\Sales\Service\V1\Data\OrderStatusHistorySearchResults
     */
    public function commentsList($id);

    /**
     * @param int $id
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStatus($id);
}
