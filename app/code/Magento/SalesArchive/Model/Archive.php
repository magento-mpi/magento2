<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales archive operations model
 */
namespace Magento\SalesArchive\Model;

class Archive
{
    /**
     * Core event manager proxy
     *
     * @var \Magento\Core\Model\Event\Manager
     */
    protected $_eventManager = null;

    /**
     * Sales archive resource archive
     *
     * @var \Magento\SalesArchive\Model\Resource\Archive
     */
    protected $_resourceArchive;

    /**
     * @param \Magento\SalesArchive\Model\Resource\Archive $resourceArchive
     * @param \Magento\Core\Model\Event\Manager $eventManager
     */
    public function __construct(
        \Magento\SalesArchive\Model\Resource\Archive $resourceArchive,
        \Magento\Core\Model\Event\Manager $eventManager
    ) {
        $this->_resourceArchive = $resourceArchive;
        $this->_eventManager = $eventManager;
    }

    /**
     * Update grid records in archive
     *
     * @param string $archiveEntity
     * @param array $ids
     * @return $this
     */
    public function updateGridRecords($archiveEntity, $ids)
    {
        $this->_resourceArchive->updateGridRecords($this, $archiveEntity, $ids);
        return $this;
    }

    /**
     * Retrieve ids in archive for specified entity
     *
     * @param string $archiveEntity
     * @param array $ids
     * @return array
     */
    public function getIdsInArchive($archiveEntity, $ids)
    {
        return $this->_resourceArchive->getIdsInArchive($archiveEntity, $ids);
    }

    /**
     * Archive orders
     *
     * @throws \Exception
     * @return \Magento\SalesArchive\Model\Archive
     */
    public function archiveOrders()
    {
        $orderIds = $this->_resourceArchive->getOrderIdsForArchiveExpression();
        $this->_resourceArchive->beginTransaction();
        try {
            $this->_resourceArchive->moveToArchive(\Magento\SalesArchive\Model\ArchivalList::ORDER,
                'entity_id', $orderIds);
            $this->_resourceArchive->moveToArchive(\Magento\SalesArchive\Model\ArchivalList::INVOICE,
                'order_id', $orderIds);
            $this->_resourceArchive->moveToArchive(\Magento\SalesArchive\Model\ArchivalList::SHIPMENT,
                'order_id', $orderIds);
            $this->_resourceArchive->moveToArchive(\Magento\SalesArchive\Model\ArchivalList::CREDITMEMO,
                'order_id', $orderIds);
            $this->_resourceArchive->removeFromGrid(\Magento\SalesArchive\Model\ArchivalList::ORDER,
                'entity_id', $orderIds);
            $this->_resourceArchive->removeFromGrid(\Magento\SalesArchive\Model\ArchivalList::INVOICE,
                'order_id', $orderIds);
            $this->_resourceArchive->removeFromGrid(\Magento\SalesArchive\Model\ArchivalList::SHIPMENT,
                'order_id', $orderIds);
            $this->_resourceArchive->removeFromGrid(\Magento\SalesArchive\Model\ArchivalList::CREDITMEMO,
                'order_id', $orderIds);
            $this->_resourceArchive->commit();
        } catch (\Exception $e) {
            $this->_resourceArchive->rollBack();
            throw $e;
        }
        $this->_eventManager->dispatch(
            'magento_salesarchive_archive_archive_orders',
            array('order_ids' => $orderIds)
        );
        return $this;
    }

    /**
     * Archive orders, returns archived order ids
     *
     * @param array $orderIds
     * @throws \Exception
     * @return array
     */
    public function archiveOrdersById($orderIds)
    {
        $orderIds = $this->_resourceArchive->getOrderIdsForArchive($orderIds, false);

        if (!empty($orderIds)) {
            $this->_resourceArchive->beginTransaction();
            try {
                $this->_resourceArchive->moveToArchive(\Magento\SalesArchive\Model\ArchivalList::ORDER,
                    'entity_id', $orderIds);
                $this->_resourceArchive->moveToArchive(\Magento\SalesArchive\Model\ArchivalList::INVOICE,
                    'order_id', $orderIds);
                $this->_resourceArchive->moveToArchive(\Magento\SalesArchive\Model\ArchivalList::SHIPMENT,
                    'order_id', $orderIds);
                $this->_resourceArchive->moveToArchive(\Magento\SalesArchive\Model\ArchivalList::CREDITMEMO,
                    'order_id', $orderIds);
                $this->_resourceArchive->removeFromGrid(\Magento\SalesArchive\Model\ArchivalList::ORDER,
                    'entity_id', $orderIds);
                $this->_resourceArchive->removeFromGrid(\Magento\SalesArchive\Model\ArchivalList::INVOICE,
                    'order_id', $orderIds);
                $this->_resourceArchive->removeFromGrid(\Magento\SalesArchive\Model\ArchivalList::SHIPMENT,
                    'order_id', $orderIds);
                $this->_resourceArchive->removeFromGrid(\Magento\SalesArchive\Model\ArchivalList::CREDITMEMO,
                    'order_id', $orderIds);
                $this->_resourceArchive->commit();
            } catch (\Exception $e) {
                $this->_resourceArchive->rollBack();
                throw $e;
            }
            $this->_eventManager->dispatch(
                'magento_salesarchive_archive_archive_orders',
                array('order_ids' => $orderIds)
            );
        }


        return $orderIds;
    }

    /**
     * Move all orders from archive grid tables to regular grid tables
     *
     * @throws \Exception
     * @return \Magento\SalesArchive\Model\Archive
     */
    public function removeOrdersFromArchive()
    {
        $this->_resourceArchive->beginTransaction();
        try {
            $this->_resourceArchive->removeFromArchive(\Magento\SalesArchive\Model\ArchivalList::ORDER);
            $this->_resourceArchive->removeFromArchive(\Magento\SalesArchive\Model\ArchivalList::INVOICE);
            $this->_resourceArchive->removeFromArchive(\Magento\SalesArchive\Model\ArchivalList::SHIPMENT);
            $this->_resourceArchive->removeFromArchive(\Magento\SalesArchive\Model\ArchivalList::CREDITMEMO);
            $this->_resourceArchive->commit();
        } catch (\Exception $e) {
            $this->_resourceArchive->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Removes orders from archive and restore in orders grid tables,
     * returns restored order ids
     *
     * @param array $orderIds
     * @throws \Exception
     * @return array
     */
    public function removeOrdersFromArchiveById($orderIds)
    {
        $orderIds = $this->_resourceArchive->getIdsInArchive(\Magento\SalesArchive\Model\ArchivalList::ORDER,
            $orderIds);

        if (!empty($orderIds)) {
            $this->_resourceArchive->beginTransaction();
            try {
                $this->_resourceArchive->removeFromArchive(\Magento\SalesArchive\Model\ArchivalList::ORDER,
                    'entity_id', $orderIds);
                $this->_resourceArchive->removeFromArchive(\Magento\SalesArchive\Model\ArchivalList::INVOICE,
                    'order_id', $orderIds);
                $this->_resourceArchive->removeFromArchive(\Magento\SalesArchive\Model\ArchivalList::SHIPMENT,
                    'order_id', $orderIds);
                $this->_resourceArchive->removeFromArchive(\Magento\SalesArchive\Model\ArchivalList::CREDITMEMO,
                    'order_id', $orderIds);
                $this->_resourceArchive->commit();
            } catch (\Exception $e) {
                $this->_resourceArchive->rollBack();
                throw $e;
            }
        }

        return $orderIds;
    }

    /**
     * Find related to order entity ids for checking of new items in archive
     *
     * @param string $archiveEntity
     * @param array $ids
     * @return array
     */
    public function getRelatedIds($archiveEntity, $ids)
    {
        return $this->_resourceArchive->getRelatedIds($this, $archiveEntity, $ids);
    }
}
