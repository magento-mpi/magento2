<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sales Rule resource model
 *
 * @category Magento
 * @package Magento_SalesRule
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Magento_SalesRule_Model_Resource_Rule extends Magento_Rule_Model_Resource_Abstract
{
    /**
     * Store associated with rule entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = array(
        'website' => array(
            'associations_table' => 'salesrule_website',
            'rule_id_field'      => 'rule_id',
            'entity_id_field'    => 'website_id'
        ),
        'customer_group' => array(
            'associations_table' => 'salesrule_customer_group',
            'rule_id_field'      => 'rule_id',
            'entity_id_field'    => 'customer_group_id'
        )
    );

    /**
     * Core string
     *
     * @var Magento_Core_Helper_String
     */
    protected $_coreString = null;

    /**
     * @var Magento_SalesRule_Model_Resource_Coupon
     */
    protected $_resourceCoupon;

    /**
     * @param Magento_Core_Helper_String $coreString
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_SalesRule_Model_Resource_Coupon $resourceCoupon
     */
    public function __construct(
        Magento_Core_Helper_String $coreString,
        Magento_Core_Model_Resource $resource,
        Magento_SalesRule_Model_Resource_Coupon $resourceCoupon
    ) {
        $this->_coreString = $coreString;
        $this->_resourceCoupon = $resourceCoupon;
        parent::__construct($resource);
    }

    /**
     * Initialize main table and table id field
     */
    protected function _construct()
    {
        $this->_init('salesrule', 'rule_id');
    }

    /**
     * Add customer group ids and website ids to rule data after load
     *
     * @param Magento_Core_Model_Abstract $object
     *
     * @return Magento_SalesRule_Model_Resource_Rule
     */
    protected function _afterLoad(Magento_Core_Model_Abstract $object)
    {
        $object->setData('customer_group_ids', (array)$this->getCustomerGroupIds($object->getId()));
        $object->setData('website_ids', (array)$this->getWebsiteIds($object->getId()));

        parent::_afterLoad($object);
        return $this;
    }

    /**
     * Prepare sales rule's discount quantity
     *
     * @param Magento_Core_Model_Abstract $object
     *
     * @return Magento_SalesRule_Model_Resource_Rule
     */
    public function _beforeSave(Magento_Core_Model_Abstract $object)
    {
        if (!$object->getDiscountQty()) {
            $object->setDiscountQty(new Zend_Db_Expr('NULL'));
        }

        parent::_beforeSave($object);
        return $this;
    }

    /**
     * Bind sales rule to customer group(s) and website(s).
     * Save rule's associated store labels.
     * Save product attributes used in rule.
     *
     * @param Magento_Core_Model_Abstract $object
     *
     * @return Magento_SalesRule_Model_Resource_Rule
     */
    protected function _afterSave(Magento_Core_Model_Abstract $object)
    {
        if ($object->hasStoreLabels()) {
            $this->saveStoreLabels($object->getId(), $object->getStoreLabels());
        }

        if ($object->hasWebsiteIds()) {
            $websiteIds = $object->getWebsiteIds();
            if (!is_array($websiteIds)) {
                $websiteIds = explode(',', (string)$websiteIds);
            }
            $this->bindRuleToEntity($object->getId(), $websiteIds, 'website');
        }

        if ($object->hasCustomerGroupIds()) {
            $customerGroupIds = $object->getCustomerGroupIds();
            if (!is_array($customerGroupIds)) {
                $customerGroupIds = explode(',', (string)$customerGroupIds);
            }
            $this->bindRuleToEntity($object->getId(), $customerGroupIds, 'customer_group');
        }

        // Save product attributes used in rule
        $ruleProductAttributes = array_merge(
            $this->getProductAttributes(serialize($object->getConditions()->asArray())),
            $this->getProductAttributes(serialize($object->getActions()->asArray()))
        );
        if (count($ruleProductAttributes)) {
            $this->setActualProductAttributes($object, $ruleProductAttributes);
        }

        // Update auto geterated specific coupons if exists
        if ($object->getUseAutoGeneration() && $object->hasDataChanges()) {
            $this->_resourceCoupon->updateSpecificCoupons($object);
        }
        return parent::_afterSave($object);
    }

    /**
     * Retrieve coupon/rule uses for specified customer
     *
     * @param Magento_SalesRule_Model_Rule $rule
     * @param int $customerId
     *
     * @return string
     */
    public function getCustomerUses($rule, $customerId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getTable('rule_customer'), array('cnt'=>'count(*)'))
            ->where('rule_id = :rule_id')
            ->where('customer_id = :customer_id');
        return $read->fetchOne($select, array(':rule_id' => $rule->getRuleId(), ':customer_id' => $customerId));
    }

    /**
     * Save rule labels for different store views
     *
     * @param int $ruleId
     * @param array $labels
     * @throws Exception
     * @return Magento_SalesRule_Model_Resource_Rule
     */
    public function saveStoreLabels($ruleId, $labels)
    {
        $deleteByStoreIds = array();
        $table   = $this->getTable('salesrule_label');
        $adapter = $this->_getWriteAdapter();

        $data    = array();
        foreach ($labels as $storeId => $label) {
            if ($this->_coreString->strlen($label)) {
                $data[] = array('rule_id' => $ruleId, 'store_id' => $storeId, 'label' => $label);
            } else {
                $deleteByStoreIds[] = $storeId;
            }
        }

        $adapter->beginTransaction();
        try {
            if (!empty($data)) {
                $adapter->insertOnDuplicate(
                    $table,
                    $data,
                    array('label')
                );
            }

            if (!empty($deleteByStoreIds)) {
                $adapter->delete($table, array(
                    'rule_id=?'       => $ruleId,
                    'store_id IN (?)' => $deleteByStoreIds
                ));
            }
        } catch (Exception $e) {
            $adapter->rollback();
            throw $e;
        }
        $adapter->commit();

        return $this;
    }

    /**
     * Get all existing rule labels
     *
     * @param int $ruleId
     * @return array
     */
    public function getStoreLabels($ruleId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('salesrule_label'), array('store_id', 'label'))
            ->where('rule_id = :rule_id');
        return $this->_getReadAdapter()->fetchPairs($select, array(':rule_id' => $ruleId));
    }

    /**
     * Get rule label by specific store id
     *
     * @param int $ruleId
     * @param int $storeId
     * @return string
     */
    public function getStoreLabel($ruleId, $storeId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('salesrule_label'), 'label')
            ->where('rule_id = :rule_id')
            ->where('store_id IN(0, :store_id)')
            ->order('store_id DESC');
        return $this->_getReadAdapter()->fetchOne($select, array(':rule_id' => $ruleId, ':store_id' => $storeId));
    }

    /**
     * Return codes of all product attributes currently used in promo rules for specified customer group and website
     *
     * @param unknown_type $websiteId
     * @param unknown_type $customerGroupId
     * @return mixed
     */
    public function getActiveAttributes($websiteId, $customerGroupId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from(array('a' => $this->getTable('salesrule_product_attribute')),
                new Zend_Db_Expr('DISTINCT ea.attribute_code'))
            ->joinInner(array('ea' => $this->getTable('eav_attribute')), 'ea.attribute_id = a.attribute_id', array());
        return $read->fetchAll($select);
    }

    /**
     * Save product attributes currently used in conditions and actions of rule
     *
     * @param Magento_SalesRule_Model_Rule $rule
     * @param mixed $attributes
     * @return Magento_SalesRule_Model_Resource_Rule
     */
    public function setActualProductAttributes($rule, $attributes)
    {
        $write = $this->_getWriteAdapter();
        $write->delete($this->getTable('salesrule_product_attribute'), array('rule_id=?' => $rule->getId()));

        //Getting attribute IDs for attribute codes
        $attributeIds = array();
        $select = $this->_getReadAdapter()->select()
            ->from(array('a' => $this->getTable('eav_attribute')), array('a.attribute_id'))
            ->where('a.attribute_code IN (?)', array($attributes));
        $attributesFound = $this->_getReadAdapter()->fetchAll($select);
        if ($attributesFound) {
            foreach ($attributesFound as $attribute) {
                $attributeIds[] = $attribute['attribute_id'];
            }

            $data = array();
            foreach ($rule->getCustomerGroupIds() as $customerGroupId) {
                foreach ($rule->getWebsiteIds() as $websiteId) {
                    foreach ($attributeIds as $attribute) {
                        $data[] = array (
                            'rule_id'           => $rule->getId(),
                            'website_id'        => $websiteId,
                            'customer_group_id' => $customerGroupId,
                            'attribute_id'      => $attribute
                        );
                    }
                }
            }
            $write->insertMultiple($this->getTable('salesrule_product_attribute'), $data);
        }

        return $this;
    }

    /**
     * Collect all product attributes used in serialized rule's action or condition
     *
     * @param string $serializedString
     *
     * @return array
     */
    public function getProductAttributes($serializedString)
    {
        $result = array();
        if (preg_match_all('~s:32:"salesrule/rule_condition_product";s:9:"attribute";s:\d+:"(.*?)"~s',
            $serializedString, $matches)){
            foreach ($matches[1] as $offset => $attributeCode) {
                $result[] = $attributeCode;
            }
        }

        return $result;
    }
}
