<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TargetRule Rule Resource Model
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_TargetRule_Model_Resource_Rule extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Constructor
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_targetrule', 'rule_id');
    }

    /**
     * Prepare target rule before save
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->getFromDate() instanceof Zend_Date) {
            $object->setFromDate($object->getFromDate()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
        } else {
            $object->setFromDate(null);
        }

        if ($object->getToDate() instanceof Zend_Date) {
            $object->setToDate($object->getToDate()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
        } else {
            $object->setToDate(null);
        }
    }

    /**
     * Save Customer Segment relations after save rule
     *
     * @param Enterprise_TargetRule_Model_Rule $object
     * @return Enterprise_TargetRule_Model_Resource_Rule
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        parent::_afterSave($object);
        $this->_prepareRuleProducts($object);

        $typeId = (!$object->isObjectNew() && $object->getOrigData('apply_to') != $object->getData('apply_to'))
            ? null
            : $object->getData('apply_to');

        Mage::getSingleton('Mage_Index_Model_Indexer')->processEntityAction(
            new Varien_Object(array('type_id' => $typeId)),
            Enterprise_TargetRule_Model_Index::ENTITY_TARGETRULE,
            Enterprise_TargetRule_Model_Index::EVENT_TYPE_CLEAN_TARGETRULES
        );

        return $this;
    }

    /**
     * Remove index before delete rule
     *
     * @param Enterprise_TargetRule_Model_Rule $object
     * @return Enterprise_TargetRule_Model_Resource_Rule
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        Mage::getSingleton('Mage_Index_Model_Indexer')->processEntityAction(
            new Varien_Object(array('type_id' => $object->getData('apply_to'))),
            Enterprise_TargetRule_Model_Index::ENTITY_TARGETRULE,
            Enterprise_TargetRule_Model_Index::EVENT_TYPE_CLEAN_TARGETRULES
        );
        return parent::_beforeDelete($object);
    }

    /**
     * Retrieve target rule and customer segment relations table name
     *
     * @return string
     */
    protected function _getCustomerSegmentRelationsTable()
    {
        return $this->getTable('enterprise_targetrule_customersegment');
    }

    /**
     * Retrieve target rule matched by condition products table name
     *
     * @return string
     */
    protected function _getRuleProductsTable()
    {
        return $this->getTable('enterprise_targetrule_product');
    }

    /**
     * Retrieve customer segment relations by target rule id
     *
     * @param int $ruleId
     * @return array
     */
    public function getCustomerSegmentRelations($ruleId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->_getCustomerSegmentRelationsTable(), 'segment_id')
            ->where('rule_id = :rule_id');
        return $adapter->fetchCol($select, array(':rule_id' => $ruleId));
    }

    /**
     * Save Customer Segment Relations
     *
     * @param Enterprise_TargetRule_Model_Rule $object
     * @return Enterprise_TargetRule_Model_Resource_Rule
     */
    protected function _saveCustomerSegmentRelations(Mage_Core_Model_Abstract $object)
    {
        $adapter = $this->_getWriteAdapter();
        $ruleId  = $object->getId();
        if (!$object->getUseCustomerSegment()) {
            $adapter->delete($this->_getCustomerSegmentRelationsTable(), array('rule_id=?' => $ruleId));
            return $this;
        }

        $old = $this->getCustomerSegmentRelations($ruleId);
        $new = $object->getCustomerSegmentRelations();

        $insert = array_diff($new, $old);
        $delete = array_diff($old, $new);

        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $segmentId) {
                $data[] = array(
                    'rule_id'       => $ruleId,
                    'segment_id'    => $segmentId
                );
            }
            $adapter->insertMultiple($this->_getCustomerSegmentRelationsTable(), $data);
        }

        if (!empty($delete)) {
            $where = array(
                'rule_id=?' => $ruleId,
                'segment_id IN(?)' => $delete
            );
            $adapter->delete($this->_getCustomerSegmentRelationsTable(), $where);
        }

        return $this;
    }

    /**
     * Prepare and Save Matched products for Rule
     *
     * @param Enterprise_TargetRule_Model_Rule $object
     * @return Enterprise_TargetRule_Model_Resource_Rule
     */
    protected function _prepareRuleProducts($object)
    {
        $adapter = $this->_getWriteAdapter();

        // remove old matched products
        $ruleId  = $object->getId();
        $adapter->delete($this->_getRuleProductsTable(), array('rule_id=?' => $ruleId));

        // retrieve and save new matched product ids
        $chunk = array_chunk($object->getMatchingProductIds(), 1000);
        foreach ($chunk as $productIds) {
            $data = array();
            foreach ($productIds as $productId) {
                $data[] = array(
                    'rule_id'       => $ruleId,
                    'product_id'    => $productId,
                    'store_id'      => 0
                );
            }
            if ($data) {
                $adapter->insertMultiple($this->_getRuleProductsTable(), $data);
            }
        }

        return $this;
    }

    /**
     * Add Customer segment relations to Rule Resource Collection
     *
     * @param Enterprise_TargetRule_Model_Resource_Rule_Collection $collection
     * @return Enterprise_TargetRule_Model_Resource_Rule
     */
    public function addCustomerSegmentRelationsToCollection(Mage_Core_Model_Resource_Db_Collection_Abstract $collection
        )
    {
        $ruleIds    = array_keys($collection->getItems());
        $segments   = array();
        if ($ruleIds) {
            $adapter = $this->_getReadAdapter();
            $select = $adapter->select()
                ->from($this->_getCustomerSegmentRelationsTable())
                ->where('rule_id IN(?)', $ruleIds);
            $rowSet = $adapter->fetchAll($select);

            foreach ($rowSet as $row) {
                $segments[$row['rule_id']][$row['segment_id']] = $row['segment_id'];
            }
        }

        foreach ($collection->getItems() as $rule) {
            /* @var $rule Enterprise_TargetRule_Model_Rule */
            if ($rule->getUseCustomerSegment()) {
                $data = isset($segments[$rule->getId()]) ? $segments[$rule->getId()] : array();
                $rule->setCustomerSegmentRelations($data);
            }
        }

        return $this;
    }
}
