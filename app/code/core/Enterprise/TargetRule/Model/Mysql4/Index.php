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
 * @category   Enterprise
 * @package    Enterprise_TargetRule
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * TargetRule Product Index by Rule Product List Type Resource Model
 *
 * @category   Enterprise
 * @package    Enterprise_TargetRule
 */
class Enterprise_TargetRule_Model_Mysql4_Index extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Initialize connection and define main table
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_targetrule/index', 'entity_id');
    }

    /**
     * Retrieve constant value overfill limit for product ids index
     *
     * @return int
     */
    public function getOverfillLimit()
    {
        return 10;
    }

    /**
     * Retrieve catalog product list index by type
     *
     * @param int $type
     * @return Enterprise_TargetRule_Model_Mysql4_Index_Abstract
     */
    public function getTypeIndex($type)
    {
        switch ($type) {
            case Enterprise_TargetRule_Model_Rule::RELATED_PRODUCTS:
                $model = 'related';
                break;

            case Enterprise_TargetRule_Model_Rule::UP_SELLS:
                $model = 'upsell';
                break;

            case Enterprise_TargetRule_Model_Rule::CROSS_SELLS:
                $model = 'crosssell';
                break;

            default:
                Mage::throwException(
                    Mage::helper('enterprise_targetrule')->__('Undefined Catalog Product List Type')
                );
        }

        return Mage::getResourceSingleton('enterprise_targetrule/index_' . $model);
    }

    /**
     * Retrieve array of defined product list type id
     *
     * @return array
     */
    public function getTypeIds()
    {
        return array(
            Enterprise_TargetRule_Model_Rule::RELATED_PRODUCTS,
            Enterprise_TargetRule_Model_Rule::UP_SELLS,
            Enterprise_TargetRule_Model_Rule::CROSS_SELLS
        );
    }

    /**
     * Retrieve product Ids
     *
     * @param Enterprise_TargetRule_Model_Index $object
     * @return array
     */
    public function getProductIds($object)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getMainTable(), 'flag')
            ->where('type_id=?', $object->getType())
            ->where('entity_id=?', $object->getProduct()->getEntityId())
            ->where('store_id=?', $object->getStoreId())
            ->where('customer_group_id=?', $object->getCustomerGroupId());
        $flag    = $adapter->fetchOne($select);

        if (empty($flag)) {
            $productIds = $this->_matchProductIds($object);
            if ($productIds) {
                $value = join(',', $productIds);
                $this->getTypeIndex($object->getType())
                    ->saveResult($object, $value);
            }
            $this->saveFlag($object);
        } else {
            $productIds = $this->getTypeIndex($object->getType())
                ->loadProductIds($object);
        }

        $productIds = array_diff($productIds, $object->getExcludeProductIds());

        return array_slice($productIds, 0, $object->getLimit());
    }

    /**
     * Match, save and return applicable product ids by index object
     *
     * @param Enterprise_TargetRule_Model_Index $object
     * @return array
     */
    protected function _matchProductIds($object)
    {
        $limit      = $object->getLimit() + $this->getOverfillLimit();
        $productIds = array();
        $ruleCollection = $object->getRuleCollection();
        foreach ($ruleCollection as $rule) {
            /* @var $rule Enterprise_TargetRule_Model_Rule */
            if (count($productIds) >= $limit) {
                continue;
            }
            if (!$rule->checkDateForStore($object->getStoreId())) {
                continue;
            }

            /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */
            $collection = Mage::getResourceSingleton('catalog/product_collection')
                ->setStoreId($object->getStoreId());
            Mage::getSingleton('catalog/product_visibility')
                ->addVisibleInCatalogFilterToCollection($collection);

            $condition = $rule->getActions()->getConditionForCollection($collection, $object);
            if ($condition) {
                $collection->getSelect()->where($condition);
            }
            if ($productIds) {
                $collection->addFieldToFilter('entity_id', array('nin' => $productIds));
            }

            $resultIds = $collection->getAllIds($limit);

            $productIds = array_merge($productIds, $resultIds);
        }

        return $productIds;
    }

    /**
     * Save index flag by index object data
     *
     * @param Enterprise_TargetRule_Model_Index $object
     * @return Enterprise_TargetRule_Model_Mysql4_Index
     */
    public function saveFlag($object)
    {
        $data = array(
            'type_id'           => $object->getType(),
            'entity_id'         => $object->getProduct()->getEntityId(),
            'store_id'          => $object->getStoreId(),
            'customer_group_id' => $object->getCustomerGroupId(),
            'flag'              => 1
        );

        $this->_getWriteAdapter()->insert($this->getMainTable(), $data);

        return $this;
    }

    /**
     * Retrieve new SELECT instance (used Read Adapter)
     *
     * @return Varien_Db_Select
     */
    public function select()
    {
        return $this->_getReadAdapter()->select();
    }

    /**
     * Retrieve SQL condition fragment by field, operator and value
     *
     * @param string $field
     * @param string $operator
     * @param int|string|array $value
     * @return string
     */
    public function getOperatorCondition($field, $operator, $value)
    {
        switch ($operator) {
            case '!=':
                $selectOperator = '<>?';
                break;

            case '>=':
                $selectOperator = '>=?';
                break;

            case '<=':
                $selectOperator = '<=?';
                break;

            case '>':
                $selectOperator = '>?';
                break;

            case '<':
                $selectOperator = '<?';
                break;

            case '{}':
                $selectOperator = 'LIKE ?';
                $value          = '%' . $value . '%';
                break;

            case '!{}':
                $selectOperator = 'NOT LIKE ?';
                $value          = '%' . $value . '%';
                break;

            case '()':
                $selectOperator = 'IN(?)';
                break;

            case '!()':
                $selectOperator = 'NOT IN(?)';
                break;

            default:
                $selectOperator = '=?';
                break;
        }

        return $this->_getReadAdapter()->quoteInto("{$field} {$selectOperator}", $value);
    }

    /**
     * Remove index by rule and type
     *
     * @param int $ruleId
     * @param int $typeId
     * @return Enterprise_TargetRule_Model_Mysql4_Index
     */
    public function removeIndexByRule($ruleId, $typeId = null)
    {
        $select  = $this->_getReadAdapter()->select()
            ->from($this->getTable('enterprise_targetrule/product'), array('product_id'))
            ->where('rule_id=?', $ruleId);

        $this->removeIndexByProductIds($select, $typeId);

        return $this;
    }

    /**
     * Remove index by product ids and type
     *
     * @param int|array|Varien_Db_Select $productIds
     * @param int $typeId
     * @return Enterprise_TargetRule_Model_Mysql4_Index
     */
    public function removeIndexByProductIds($productIds, $typeId = null)
    {
        $adapter = $this->_getWriteAdapter();

        $where = array(
            'entity_id IN(?)'   => $productIds
        );

        if (is_null($typeId)) {
            foreach ($this->getTypeIds() as $typeId) {
                $this->getTypeIndex($typeId)->removeIndex($productIds);
            }
        } else {
            $this->getTypeIndex($typeId)->removeIndex($productIds);
            $where['type_id=?'] = $typeId;
        }

        $adapter->delete($this->getMainTable(), $where);
    }

    /**
     * Remove target rule matched product index data by product id or/and rule id
     *
     * @param int $productId
     * @param int $ruleId
     * @return Enterprise_TargetRule_Model_Mysql4_Index
     */
    public function removeProductIndex($productId = null, $ruleId = null)
    {
        $adapter = $this->_getWriteAdapter();
        $where   = array();
        if (!is_null($productId)) {
            $where['product_id=?'] = $productId;
        }
        if (!is_null($ruleId)) {
            $where['rule_id=?'] = $ruleId;
        }

        $adapter->delete($this->getTable('enterprise_targetrule/product'), $where);

        return $this;
    }

    /**
     * Save target rule matched product index data
     *
     * @param int $ruleId
     * @param int $productId
     */
    public function saveProductIndex($ruleId, $productId)
    {
        $this->removeProductIndex($productId, $ruleId);

        $adapter = $this->_getWriteAdapter();
        $bind    = array(
            'rule_id'       => $ruleId,
            'product_id'    => $productId
        );

        $adapter->insert($this->getTable('enterprise_targetrule/product'), $bind);

        return $this;
    }
}
