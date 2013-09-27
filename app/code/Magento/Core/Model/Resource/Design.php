<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Core Design Resource Model
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_Resource_Design extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table and primary key
     *
     */
    protected function _construct()
    {
        $this->_init('design_change', 'design_change_id');
    }

    /**
     * Perform actions before object save
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Core_Model_Resource_Db_Abstract
     * @throws Magento_Core_Exception
     */
    public function _beforeSave(Magento_Core_Model_Abstract $object)
    {
        if ($date = $object->getDateFrom()) {
            $object->setDateFrom($this->formatDate($date));
        } else {
            $object->setDateFrom(null);
        }

        if ($date = $object->getDateTo()) {
            $object->setDateTo($this->formatDate($date));
        } else {
            $object->setDateTo(null);
        }

        if (!is_null($object->getDateFrom())
            && !is_null($object->getDateTo())
            && Magento_Date::toTimestamp($object->getDateFrom()) > Magento_Date::toTimestamp($object->getDateTo())) {
            throw new Magento_Core_Exception(__('Start date cannot be greater than end date.'));
        }

        $check = $this->_checkIntersection(
            $object->getStoreId(),
            $object->getDateFrom(),
            $object->getDateTo(),
            $object->getId()
        );

        if ($check) {
            throw new Magento_Core_Exception(
                __('Your design change for the specified store intersects with another one, please specify another date range.')
            );
        }

        if ($object->getDateFrom() === null) {
            $object->setDateFrom(new Zend_Db_Expr('null'));
        }
        if ($object->getDateTo() === null) {
            $object->setDateTo(new Zend_Db_Expr('null'));
        }

        parent::_beforeSave($object);
    }


    /**
     * Check intersections
     *
     * @param int $storeId
     * @param date $dateFrom
     * @param date $dateTo
     * @param int $currentId
     * @return Array
     */
    protected function _checkIntersection($storeId, $dateFrom, $dateTo, $currentId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from(array('main_table'=>$this->getTable('design_change')))
            ->where('main_table.store_id = :store_id')
            ->where('main_table.design_change_id <> :current_id');

        $dateConditions = array('date_to IS NULL AND date_from IS NULL');

        if (!is_null($dateFrom)) {
            $dateConditions[] = ':date_from BETWEEN date_from AND date_to';
            $dateConditions[] = ':date_from >= date_from and date_to IS NULL';
            $dateConditions[] = ':date_from <= date_to and date_from IS NULL';
        } else {
            $dateConditions[] = 'date_from IS NULL';
        }

        if (!is_null($dateTo)) {
            $dateConditions[] = ':date_to BETWEEN date_from AND date_to';
            $dateConditions[] = ':date_to >= date_from AND date_to IS NULL';
            $dateConditions[] = ':date_to <= date_to AND date_from IS NULL';
        } else {
            $dateConditions[] = 'date_to IS NULL';
        }

        if (is_null($dateFrom) && !is_null($dateTo)) {
            $dateConditions[] = 'date_to <= :date_to OR date_from <= :date_to';
        }

        if (!is_null($dateFrom) && is_null($dateTo)) {
            $dateConditions[] = 'date_to >= :date_from OR date_from >= :date_from';
        }

        if (!is_null($dateFrom) && !is_null($dateTo)) {
            $dateConditions[] = 'date_from BETWEEN :date_from AND :date_to';
            $dateConditions[] = 'date_to BETWEEN :date_from AND :date_to';
        } elseif (is_null($dateFrom) && is_null($dateTo)) {
            $dateConditions = array();
        }

        $condition = '';
        if (!empty($dateConditions)) {
            $condition = '(' . implode(') OR (', $dateConditions) . ')';
            $select->where($condition);
        }

        $bind = array(
            'store_id'   => (int)$storeId,
            'current_id' => (int)$currentId,
        );

        if (!is_null($dateTo)) {
            $bind['date_to'] = $dateTo;
        }
        if (!is_null($dateFrom)) {
            $bind['date_from'] = $dateFrom;
        }

        $result = $adapter->fetchOne($select, $bind);
        return $result;
    }

    /**
     * Load changes for specific store and date
     *
     * @param int $storeId
     * @param string $date
     * @return array
     */
    public function loadChange($storeId, $date)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('main_table' => $this->getTable('design_change')))
            ->where('store_id = :store_id')
            ->where('date_from <= :required_date or date_from IS NULL')
            ->where('date_to >= :required_date or date_to IS NULL');

        $bind = array(
            'store_id'      => (int)$storeId,
            'required_date' => $date
        );

        return $this->_getReadAdapter()->fetchRow($select, $bind);
    }
}
