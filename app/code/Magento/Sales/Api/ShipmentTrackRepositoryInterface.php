<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Sales\Api;

/**
 * Shipment track repository interface.
 *
 * A shipment is a delivery package that contains products. A shipment document accompanies the shipment. This
 * document lists the products and their quantities in the delivery package.
 */
interface ShipmentTrackRepositoryInterface
{
    /**
     * Lists shipment tracks that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteria $criteria The search criteria.
     * @return \Magento\Sales\Api\Data\ShipmentTrackSearchResultInterface Shipment track search result interface.
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $criteria);

    /**
     * Loads a specified shipment track.
     *
     * @param int $id The shipment track ID.
     * @return \Magento\Sales\Api\Data\ShipmentTrackInterface Shipment track interface.
     */
    public function get($id);

    /**
     * Deletes a specified shipment track.
     *
     * @param \Magento\Sales\Api\Data\ShipmentTrackInterface $entity The shipment track.
     * @return bool
     */
    public function delete(\Magento\Sales\Api\Data\ShipmentTrackInterface $entity);

    /**
     * Performs persist operations for a specified shipment track.
     *
     * @param \Magento\Sales\Api\Data\ShipmentTrackInterface $entity The shipment track.
     * @return \Magento\Sales\Api\Data\ShipmentTrackInterface Shipment track interface.
     */
    public function save(\Magento\Sales\Api\Data\ShipmentTrackInterface $entity);

    /**
     * Deletes a specified shipment track by ID.
     *
     * @param int $id The shipment track ID.
     * @return bool
     */
    public function deleteById($id);
}
