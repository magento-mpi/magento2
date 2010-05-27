<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Gift registry entity resource model
 */
class Enterprise_GiftRegistry_Model_Mysql4_Entity extends Enterprise_Enterprise_Model_Core_Mysql4_Abstract
{
    /**
     * Event table name
     *
     * @var string
     */
    protected $_eventTable;

    /**
     * Assigning eventTable
     */
    protected function _construct() {
        $this->_init('enterprise_giftregistry/entity', 'entity_id');
        $this->_eventTable = $this->getTable('enterprise_giftregistry/data');
    }

    /**
     * Converting some data to internal database format
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $customValues = $object->getCustomValues();
        if ($object->getId()) {
            $dateFields = $object->getCustomDateFields();
            foreach ($dateFields as $fieldId) {
                if (is_array($customValues) && key_exists($fieldId, $customValues)) {
                    $customValues[$fieldId] = $this->formatDate($customValues[$fieldId]);
                }
            }

        }
        $object->setCustomValues(serialize($customValues));
        return parent::_beforeSave($object);
    }

    /**
     * Fetching data from event table at same time as from entity table
     *
     * @param   string $field
     * @param   mixed $value
     * @return  Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $select->joinLeft(array('event_table' => $this->_eventTable),
            'event_table.'.$this->getIdFieldName().'='.$this->getMainTable().'.'.$this->getIdFieldName(), '*');
        return $select;
    }

    /**
     * Perform actions after object is loaded
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            $object->setType($object->getData('type_id'));
            $object->setCustomValues(unserialize($object->getCustomValues()));
        }
        return parent::_afterLoad($object);
    }

    /**
     * Perform action after object is saved - saving data to the eventTable
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $data = array(
            'event_region_id' => $object->getEventRegionId(),
            'event_region' => $object->getEventRegion(),
            'event_date' => $object->getEventDate(),
            'event_location' => $object->getEventLocation(),
            'event_country_code'  => $object->getEventCountryCode());
        $updateFields = array_keys($data);
        if ($object->getId()) {
            $data['entity_id'] = $object->getId();
            $this->_getWriteAdapter()->insertOnDuplicate($this->_eventTable, $data, $updateFields);
        }
        return parent::_afterSave($object);
    }

    /**
     * Fetches typeId for entity
     *
     * @param int
     * @return string
     */
    public function getTypeIdByEntityId($entityId)
    {
        return $this->_getReadAdapter()->fetchOne(
            $this->_getReadAdapter()->select()
                ->from($this->getMainTable(), 'type_id')
                ->where('entity_id = ?', $entityId));
    }

    /**
     * Set active entity filtered by customer
     *
     * @param int $customerId
     * @param int $entityId
     * @return Enterprise_GiftRegistry_Model_Mysql4_Entity
     */
    public function setActiveEntity($customerId, $entityId)
    {
        $adapter = $this->_getWriteAdapter();
        $adapter->update($this->getMainTable(),
            array('is_active' => '0'),
            array('customer_id =?' => $customerId)
        );
        $adapter->update($this->getMainTable(),
            array('is_active' => '1'),
            array('customer_id =?' => $customerId, 'entity_id =?' => $entityId)
        );
        return $this;
    }

    /**
     * Search
     *
     * @param array $params
     * @return array
     */
    public function quickSearch($params)
    {
        $adapter = $this->_getReadAdapter();

        $personTable = $this->getTable('enterprise_giftregistry/person');
        $dataTable = $this->getTable('enterprise_giftregistry/data');
        $infoTable = $this->getTable('enterprise_giftregistry/info');

        $select = $adapter->select()
            ->from(array('e' => $this->getMainTable()), array('entity_id', 'title'))
            ->where('e.is_public = 1')
            ->where('e.website_id=?', $params['website_id'])
            ->where('e.type_id=?', $params['type_id']);

        $select->joinInner(
            array('p' => $personTable),
            'e.entity_id = p.entity_id',
            array('registrant' => new Zend_Db_Expr("CONCAT(firstname,' ',lastname)"))
        );
        $select->where($adapter->quoteInto('p.firstname LIKE ?', $params['firstname'] . '%'));
        $select->where($adapter->quoteInto('p.lastname LIKE ?', $params['lastname'] . '%'));

        $select->joinLeft(
            array('d' => $dataTable),
            'e.entity_id = p.entity_id',
            array('event_date', 'event_location')
        );

        /*
         * Join registry type store label
         */
        $select->joinLeft(
            array('i1' => $infoTable),
            'i1.type_id = e.type_id AND i1.store_id = 0',
            array()
        );
        $select->joinLeft(
            array('i2' => $infoTable),
            'i2.type_id = e.type_id AND i2.store_id = ' . $params['store_id'],
            array('type' => new Zend_Db_Expr('IFNULL(i2.label, i1.label)'))
        );

       return $adapter->fetchAll($select);
    }
}
