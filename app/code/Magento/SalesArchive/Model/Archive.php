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
class Magento_SalesArchive_Model_Archive
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
            'model' => 'Magento_Sales_Model_Order',
            'resource_model' => 'Magento_Sales_Model_Resource_Order'
        ),
        self::INVOICE => array(
            'model' => 'Magento_Sales_Model_Order_Invoice',
            'resource_model' => 'Magento_Sales_Model_Resource_Order_Invoice'
        ),
        self::SHIPMENT  => array(
            'model' => 'Magento_Sales_Model_Order_Shipment',
            'resource_model' => 'Magento_Sales_Model_Resource_Order_Shipment'
        ),
        self::CREDITMEMO => array(
            'model' => 'Magento_Sales_Model_Order_Creditmemo',
            'resource_model' => 'Magento_Sales_Model_Resource_Order_Creditmemo'
        )
    );

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager = null;

    /**
     * @var Magento_SalesArchive_Model_Resource_Archive
     */
    protected $_archiveResource;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_SalesArchive_Model_Resource_Archive $archiveResource
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_SalesArchive_Model_Resource_Archive $archiveResource
    ) {
        $this->_archiveResource = $archiveResource;
        $this->_eventManager = $eventManager;
    }

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
     * Update grid records in archive
     *
     * @param string $archiveEntity
     * @param array $ids
     * @return $this
     */
    public function updateGridRecords($archiveEntity, $ids)
    {
        $this->_archiveResource->updateGridRecords($this, $archiveEntity, $ids);
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
        return $this->_archiveResource->getIdsInArchive($archiveEntity, $ids);
    }

    /**
     * Detects archive entity by object class
     *
     * @param Magento_Object $object
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
     * @return $this
     * @throws Exception
     */
    public function archiveOrders()
    {
        $orderIds = $this->_archiveResource->getOrderIdsForArchiveExpression();
        $this->_archiveResource->beginTransaction();
        try {
            $this->_archiveResource->moveToArchive($this, self::ORDER, 'entity_id', $orderIds);
            $this->_archiveResource->moveToArchive($this, self::INVOICE, 'order_id', $orderIds);
            $this->_archiveResource->moveToArchive($this, self::SHIPMENT, 'order_id', $orderIds);
            $this->_archiveResource->moveToArchive($this, self::CREDITMEMO, 'order_id', $orderIds);
            $this->_archiveResource->removeFromGrid($this, self::ORDER, 'entity_id', $orderIds);
            $this->_archiveResource->removeFromGrid($this, self::INVOICE, 'order_id', $orderIds);
            $this->_archiveResource->removeFromGrid($this, self::SHIPMENT, 'order_id', $orderIds);
            $this->_archiveResource->removeFromGrid($this, self::CREDITMEMO, 'order_id', $orderIds);
            $this->_archiveResource->commit();
        } catch (Exception $e) {
            $this->_archiveResource->rollBack();
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
     * @return array
     * @throws Exception
     */
    public function archiveOrdersById($orderIds)
    {
        $orderIds = $this->_archiveResource->getOrderIdsForArchive($orderIds, false);

        if (!empty($orderIds)) {
            $this->_archiveResource->beginTransaction();
            try {
                $this->_archiveResource->moveToArchive($this, self::ORDER, 'entity_id', $orderIds);
                $this->_archiveResource->moveToArchive($this, self::INVOICE, 'order_id', $orderIds);
                $this->_archiveResource->moveToArchive($this, self::SHIPMENT, 'order_id', $orderIds);
                $this->_archiveResource->moveToArchive($this, self::CREDITMEMO, 'order_id', $orderIds);
                $this->_archiveResource->removeFromGrid($this, self::ORDER, 'entity_id', $orderIds);
                $this->_archiveResource->removeFromGrid($this, self::INVOICE, 'order_id', $orderIds);
                $this->_archiveResource->removeFromGrid($this, self::SHIPMENT, 'order_id', $orderIds);
                $this->_archiveResource->removeFromGrid($this, self::CREDITMEMO, 'order_id', $orderIds);
                $this->_archiveResource->commit();
            } catch (Exception $e) {
                $this->_archiveResource->rollBack();
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
     * @return $this
     * @throws Exception
     */
    public function removeOrdersFromArchive()
    {
        $this->_archiveResource->beginTransaction();
        try {
            $this->_archiveResource->removeFromArchive($this, self::ORDER);
            $this->_archiveResource->removeFromArchive($this, self::INVOICE);
            $this->_archiveResource->removeFromArchive($this, self::SHIPMENT);
            $this->_archiveResource->removeFromArchive($this, self::CREDITMEMO);
            $this->_archiveResource->commit();
        } catch (Exception $e) {
            $this->_archiveResource->rollBack();
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
     * @throws Exception
     */
    public function removeOrdersFromArchiveById($orderIds)
    {
        $orderIds = $this->_archiveResource->getIdsInArchive(self::ORDER, $orderIds);

        if (!empty($orderIds)) {
            $this->_archiveResource->beginTransaction();
            try {
                $this->_archiveResource->removeFromArchive($this, self::ORDER, 'entity_id', $orderIds);
                $this->_archiveResource->removeFromArchive($this, self::INVOICE, 'order_id', $orderIds);
                $this->_archiveResource->removeFromArchive($this, self::SHIPMENT, 'order_id', $orderIds);
                $this->_archiveResource->removeFromArchive($this, self::CREDITMEMO, 'order_id', $orderIds);
                $this->_archiveResource->commit();
            } catch (Exception $e) {
                $this->_archiveResource->rollBack();
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
        return $this->_archiveResource->getRelatedIds($this, $archiveEntity, $ids);
    }
}
