<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftRegistry\Model\Resource;

/**
 * Gift registry entity resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Entity extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Event table name
     *
     * @var string
     */
    protected $_eventTable;

    /**
     * Assigning eventTable
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_giftregistry_entity', 'entity_id');
        $this->_eventTable = $this->getTable('magento_giftregistry_data');
    }

    /**
     * Converting some data to internal database format
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $customValues = $object->getCustomValues();
        $object->setCustomValues(serialize($customValues));
        return parent::_beforeSave($object);
    }

    /**
     * Fetching data from event table at same time as from entity table
     *
     * @param string $field
     * @param mixed $value
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $this->_joinEventData($select);

        return $select;
    }

    /**
     * Join event table to select object
     *
     * @param \Magento\Framework\DB\Select $select
     * @return \Magento\Framework\DB\Select
     */
    protected function _joinEventData($select)
    {
        $joinCondition = sprintf('e.%1$s = %2$s.%1$s', $this->getIdFieldName(), $this->getMainTable());
        $select->joinLeft(['e' => $this->_eventTable], $joinCondition, '*');
        return $select;
    }

    /**
     * Perform actions after object is loaded
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $object->setTypeById($object->getData('type_id'));
            $object->setCustomValues(unserialize($object->getCustomValues()));
        }
        return parent::_afterLoad($object);
    }

    /**
     * Perform action after object is saved - saving data to the eventTable
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $data = [];
        foreach ($object->getStaticTypeIds() as $code) {
            $objectData = $object->getData($code);
            if ($objectData) {
                $data[$code] = $objectData;
            }
        }

        if ($object->getId()) {
            $data['entity_id'] = (int)$object->getId();
            $this->_getWriteAdapter()->insertOnDuplicate($this->_eventTable, $data, array_keys($data));
        }
        return parent::_afterSave($object);
    }

    /**
     * Fetches typeId for entity
     *
     * @param int $entityId
     * @return string
     */
    public function getTypeIdByEntityId($entityId)
    {
        $select = $this->_getReadAdapter()->select()->from(
            $this->getMainTable(),
            'type_id'
        )->where(
            $this->getIdFieldName() . ' = :entity_id'
        );
        return $this->_getReadAdapter()->fetchOne($select, [':entity_id' => $entityId]);
    }

    /**
     * Fetches websiteId for entity
     *
     * @param int $entityId
     * @return string
     */
    public function getWebsiteIdByEntityId($entityId)
    {
        $select = $this->_getReadAdapter()->select()->from(
            $this->getMainTable(),
            'website_id'
        )->where(
            $this->getIdFieldName() . ' = :entity_id'
        );
        return $this->_getReadAdapter()->fetchOne($select, [':entity_id' => (int)$entityId]);
    }

    /**
     * Set active entity filtered by customer
     *
     * @param int $customerId
     * @param int $entityId
     * @return $this
     */
    public function setActiveEntity($customerId, $entityId)
    {
        $adapter = $this->_getWriteAdapter();
        $adapter->update(
            $this->getMainTable(),
            ['is_active' => new \Zend_Db_Expr('0')],
            ['customer_id =?' => (int)$customerId]
        );
        $adapter->update(
            $this->getMainTable(),
            ['is_active' => new \Zend_Db_Expr('1')],
            ['customer_id =?' => (int)$customerId, 'entity_id = ?' => (int)$entityId]
        );
        return $this;
    }

    /**
     * Load entity by gift registry item id
     *
     * @param \Magento\GiftRegistry\Model\Entity $object
     * @param int $itemId
     * @return $this
     */
    public function loadByEntityItem($object, $itemId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from(['e' => $this->getMainTable()]);
        $select->joinInner(
            ['i' => $this->getTable('magento_giftregistry_item')],
            'e.entity_id = i.entity_id AND i.item_id = :item_id',
            []
        );

        $data = $adapter->fetchRow($select, [':item_id' => (int)$itemId]);
        if ($data) {
            $object->setData($data);
            $this->_afterLoad($object);
        }
        return $this;
    }

    /**
     * Load entity by url key
     *
     * @param \Magento\GiftRegistry\Model\Entity $object
     * @param string $urlKey
     * @return $this
     */
    public function loadByUrlKey($object, $urlKey)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from($this->getMainTable())->where('url_key = :url_key');

        $this->_joinEventData($select);

        $data = $adapter->fetchRow($select, [':url_key' => $urlKey]);
        if ($data) {
            $object->setData($data);
            $this->_afterLoad($object);
        }

        return $this;
    }
}
