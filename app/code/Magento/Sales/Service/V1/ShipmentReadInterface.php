<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Framework\Service\V1\Data\SearchCriteria;

interface ShipmentReadInterface
{
    /**
     * @param int $id
     * @return \Magento\Sales\Service\V1\Data\Shipment
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id);

    /**
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Framework\Service\V1\Data\SearchResults
     */
    public function search(SearchCriteria $searchCriteria);

    /**
     * @param int $id
     * @return \Magento\Sales\Service\V1\Data\CommentSearchResults
     */
    public function commentsList($id);
}
