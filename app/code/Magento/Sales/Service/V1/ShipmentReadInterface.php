<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Framework\Api\SearchCriteria;

interface ShipmentReadInterface
{
    /**
     * @param int $id
     * @return \Magento\Sales\Service\V1\Data\Shipment
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id);

    /**
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magento\Framework\Api\SearchResults
     */
    public function search(SearchCriteria $searchCriteria);

    /**
     * @param int $id
     * @return \Magento\Sales\Service\V1\Data\CommentSearchResults
     */
    public function commentsList($id);

    /**
     * @param int $id
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getLabel($id);
}
