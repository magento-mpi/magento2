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
    const ORDER     = 'order';
    const INVOICE   = 'invoice';
    const SHIPMENT  = 'shipment';
    const CREDITMEMO= 'creditmemo';

    /**
     * Archive entities definition
     *
     * @var $_entities array
     */
    protected $_entities = array(
        self::ORDER => array(
            'model' => '\Magento\Sales\Model\Order',
            'resource_model' => '\Magento\Sales\Model\Resource\Order'
        ),
        self::INVOICE => array(
            'model' => '\Magento\Sales\Model\Order\Invoice',
            'resource_model' => '\Magento\Sales\Model\Resource\Order\Invoice'
        ),
        self::SHIPMENT  => array(
            'model' => '\Magento\Sales\Model\Order\Shipment',
            'resource_model' => '\Magento\Sales\Model\Resource\Order\Shipment'
        ),
        self::CREDITMEMO => array(
            'model' => '\Magento\Sales\Model\Order\Creditmemo',
            'resource_model' => '\Magento\Sales\Model\Resource\Order\Creditmemo'
        )
    );

    /**
     * Returns resource model class of an entity
     *
     * @param string $entity
     * @return string | false
     */
    public function getEntityResourceModel($entity)
    {
        return isset($this->_entities[$entity]) ? $this->_entities[$entity]['resource_model'] : false;
    }

    /**
     * Get archive resource model
     * @return \Magento\SalesArchive\Model\Resource\Archive
     */
    protected function _getResource()
    {
        return \Mage::getResourceSingleton('Magento\SalesArchive\Model\Resource\Archive');
    }

    /**
     * Update grid records in archive
     *
     * @param string $archiveEntity
     * @param array $ids
     */
    public function updateGridRecords($archiveEntity, $ids)
    {
        $this->_getResource()->updateGridRecords($this, $archiveEntity, $ids);
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
        return $this->_getResource()->getIdsInArchive($archiveEntity, $ids);
    }

    /**
     * Detects archive entity by object class
     *
     * @param \Magento\Object $object
     * @return string|boolean
     */
    public function detectArchiveEntity($object)
    {
        $keys = array('model', 'resource_model');
        foreach ($this->_entities as $archiveEntity => $entityClasses) {
            foreach ($keys as $key) {
                $className = $entityClasses[$key];
                if ($object instanceof $className) {
                    return $archiveEntity;
                }
            }
        }
        return false;
    }

    /**
     * Archive orders
     *
     * @return \Magento\SalesArchive\Model\Archive
     */
    public function archiveOrders()
    {
        $orderIds = $this->_getResource()->getOrderIdsForArchiveExpression();
        $this->_getResource()->beginTransaction();
        try {
            $this->_getResource()->moveToArchive($this, self::ORDER, 'entity_id', $orderIds);
            $this->_getResource()->moveToArchive($this, self::INVOICE, 'order_id', $orderIds);
            $this->_getResource()->moveToArchive($this, self::SHIPMENT, 'order_id', $orderIds);
            $this->_getResource()->moveToArchive($this, self::CREDITMEMO, 'order_id', $orderIds);
            $this->_getResource()->removeFromGrid($this, self::ORDER, 'entity_id', $orderIds);
            $this->_getResource()->removeFromGrid($this, self::INVOICE, 'order_id', $orderIds);
            $this->_getResource()->removeFromGrid($this, self::SHIPMENT, 'order_id', $orderIds);
            $this->_getResource()->removeFromGrid($this, self::CREDITMEMO, 'order_id', $orderIds);
            $this->_getResource()->commit();
        } catch (\Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }
        \Mage::dispatchEvent(
            'magento_salesarchive_archive_archive_orders',
            array('order_ids' => $orderIds)
        );
        return $this;
    }

    /**
     * Archive orders, returns archived order ids
     *
     * @param array $orderIds
     * @return array
     */
    public function archiveOrdersById($orderIds)
    {
        $orderIds = $this->_getResource()->getOrderIdsForArchive($orderIds, false);

        if (!empty($orderIds)) {
            $this->_getResource()->beginTransaction();
            try {
                $this->_getResource()->moveToArchive($this, self::ORDER, 'entity_id', $orderIds);
                $this->_getResource()->moveToArchive($this, self::INVOICE, 'order_id', $orderIds);
                $this->_getResource()->moveToArchive($this, self::SHIPMENT, 'order_id', $orderIds);
                $this->_getResource()->moveToArchive($this, self::CREDITMEMO, 'order_id', $orderIds);
                $this->_getResource()->removeFromGrid($this, self::ORDER, 'entity_id', $orderIds);
                $this->_getResource()->removeFromGrid($this, self::INVOICE, 'order_id', $orderIds);
                $this->_getResource()->removeFromGrid($this, self::SHIPMENT, 'order_id', $orderIds);
                $this->_getResource()->removeFromGrid($this, self::CREDITMEMO, 'order_id', $orderIds);
                $this->_getResource()->commit();
            } catch (\Exception $e) {
                $this->_getResource()->rollBack();
                throw $e;
            }
            \Mage::dispatchEvent(
                'magento_salesarchive_archive_archive_orders',
                array('order_ids' => $orderIds)
            );
        }


        return $orderIds;
    }

    /**
     * Move all orders from archive grid tables to regular grid tables
     *
     * @return \Magento\SalesArchive\Model\Archive
     */
    public function removeOrdersFromArchive()
    {
        $this->_getResource()->beginTransaction();
        try {
            $this->_getResource()->removeFromArchive($this, self::ORDER);
            $this->_getResource()->removeFromArchive($this, self::INVOICE);
            $this->_getResource()->removeFromArchive($this, self::SHIPMENT);
            $this->_getResource()->removeFromArchive($this, self::CREDITMEMO);
            $this->_getResource()->commit();
        } catch (\Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Removes orders from archive and restore in orders grid tables,
     * returns restored order ids
     *
     * @param array $orderIds
     * @return array
     */
    public function removeOrdersFromArchiveById($orderIds)
    {
        $orderIds = $this->_getResource()->getIdsInArchive(self::ORDER, $orderIds);

        if (!empty($orderIds)) {
            $this->_getResource()->beginTransaction();
            try {
                $this->_getResource()->removeFromArchive($this, self::ORDER, 'entity_id', $orderIds);
                $this->_getResource()->removeFromArchive($this, self::INVOICE, 'order_id', $orderIds);
                $this->_getResource()->removeFromArchive($this, self::SHIPMENT, 'order_id', $orderIds);
                $this->_getResource()->removeFromArchive($this, self::CREDITMEMO, 'order_id', $orderIds);
                $this->_getResource()->commit();
            } catch (\Exception $e) {
                $this->_getResource()->rollBack();
                throw $e;
            }

            \Mage::dispatchEvent(
                'magento_salesarchive_archive_remove_orders_from_archive',
                array('order_ids' => $orderIds)
            );
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
        return $this->_getResource()->getRelatedIds($this, $archiveEntity, $ids);
    }
}
