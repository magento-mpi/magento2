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
 * @package     Enterprise_Reminder
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Reminder data resource model
 */
class Enterprise_Reminder_Model_Mysql4_Rule extends Enterprise_Enterprise_Model_Core_Mysql4_Abstract
{
    /**
     * Rule websites table name
     *
     * @var string
     */
    protected $_websiteTable;

    /**
     * Intialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('enterprise_reminder/rule', 'rule_id');
        $this->_websiteTable = $this->getTable('enterprise_reminder/website');
    }

    /**
     * Get empty select object
     *
     * @return Varien_Db_Select
     */
    public function createSelect()
    {
        return $this->_getReadAdapter()->select();
    }

    /**
     * Quote parameters into condition string
     *
     * @param string $string
     * @param string | array $param
     * @return string unknown_type
     */
    public function quoteInto($string, $param)
    {
        return $this->_getReadAdapter()->quoteInto($string, $param);
    }

    /**
     * Prepare object data for saving
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getActiveFrom()) {
            $object->setActiveFrom(new Zend_Db_Expr('NULL'));
        }
        else {
            if ($object->getActiveFrom() instanceof Zend_Date) {
                $object->setActiveFrom($object->getActiveFrom()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
            }
        }

        if (!$object->getActiveTo()) {
            $object->setActiveTo(new Zend_Db_Expr('NULL'));
        }
        else {
            if ($object->getActiveTo() instanceof Zend_Date) {
                $object->setActiveTo($object->getActiveTo()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
            }
        }
        parent::_beforeSave($object);
    }

    /**
     * Perform actions after object save
     *
     * @param Mage_Core_Model_Abstract $rule
     */
    protected function _afterSave(Mage_Core_Model_Abstract $rule)
    {
        if ($rule->hasData('website_ids')) {
            $this->_saveWebsiteIds($rule);
        }
        if ($rule->hasData('store_templates')) {
            $this->_saveStoreData($rule);
        }
        return parent::_afterSave($rule);
    }

    /**
     * Save all website ids associated to rule
     *
     * @param $rule
     * @return unknown_type
     */
    protected function _saveWebsiteIds($rule)
    {
        $adapter = $this->_getWriteAdapter();
        $adapter->delete($this->_websiteTable, array('rule_id=?'=>$rule->getId()));
        foreach ($rule->getWebsiteIds() as $websiteId) {
            $adapter->insert($this->_websiteTable, array(
                'website_id' => $websiteId,
                'rule_id' => $rule->getId()
            ));
        }
        return $this;
    }

    /**
     * Get website ids associated to the rule id
     *
     * @param   int $ruleId
     * @return  array
     */
    public function getWebsiteIds($ruleId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->_websiteTable, 'website_id')
            ->where('rule_id=?', $ruleId);
        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Save store templates
     *
     * @param $rule
     * @return unknown_type
     */
    protected function _saveStoreData($rule)
    {
        $adapter = $this->_getWriteAdapter();
        $templateTable = $this->getTable('enterprise_reminder/template');
        $adapter->delete($templateTable, array('rule_id=?'=>$rule->getId()));

        $labels = $rule->getStoreLabels();
        $descriptions = $rule->getStoreDescriptions();

        foreach ($rule->getStoreTemplates() as $storeId=>$templateId) {
            if (!$templateId) {
                continue;
            }
            if (!is_numeric($templateId)) {
                $templateId = null;
            }
            $adapter->insert($templateTable, array(
                'rule_id'     => $rule->getId(),
                'store_id'    => $storeId,
                'template_id' => $templateId,
                'label'       => $labels[$storeId],
                'description' => $descriptions[$storeId]
            ));
        }
        return $this;
    }

    /**
     * Get templates assigned assigned to reminder rule
     *
     * @param   int $actionId
     * @return  array
     */
    public function getStoreData($ruleId)
    {
        $templateTable = $this->getTable('enterprise_reminder/template');
        $select = $this->_getReadAdapter()->select()
            ->from($templateTable, array('store_id', 'template_id', 'label', 'description'))
            ->where('rule_id=?', $ruleId);
        return $this->_getReadAdapter()->fetchAll($select);
    }

    /**
     * Get comparison condition for rule condition operator which will be used in SQL query
     *
     * @param string $operator
     * @return string
     */
    public function getSqlOperator($operator)
    {
        switch ($operator) {
            case '==':
                return '=';
            case '!=':
                return '<>';
            case '{}':
                return 'LIKE';
            case '!{}':
                return 'NOT LIKE';
            case 'between':
                return "BETWEEN '%s' AND '%s'";
            case '>':
            case '<':
            case '>=':
            case '<=':
                return $operator;
            default:
                Mage::throwException(Mage::helper('enterprise_reminder')->__('Unknown operator specified'));
        }
    }

    /**
     * Create string for select "where" condition based on field name, comparison operator and vield value
     *
     * @param string $field
     * @param string $operator
     * @param mixed $value
     * @return string
     */
    public function createConditionSql($field, $operator, $value)
    {
        $sqlOperator = $this->getSqlOperator($operator);
        $condition = '';
        switch ($operator) {
            case '{}':
            case '!{}':
                if (is_array($value)) {
                    if (!empty($value)) {
                        $sqlOperator = ($operator == '{}') ? 'IN' : 'NOT IN';
                        $condition = $this->quoteInto($field.' '.$sqlOperator.' (?)', $value);
                    }
                } else {
                    $condition = $this->quoteInto($field.' '.$sqlOperator.' ?', '%'.$value.'%');
                }
                break;
            case 'between':
                $condition = $field.' '.sprintf($sqlOperator, $value['start'], $value['end']);
                break;
            default:
                $condition = $this->quoteInto($field.' '.$sqlOperator.' ?', $value);
                break;
        }
        return $condition;
    }

    /**
     * Get active reminder rules
     *
     * @return array
     */
    public function getActiveRules()
    {
        $now = $this->formatDate(time());

        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('enterprise_reminder/rule'), array('rule_id','salesrule_id','condition_sql'))
            ->where($this->quoteInto('active_from IS NULL OR active_from <= ?', $now))
            ->where($this->quoteInto('active_to IS NULL OR active_to >= ?', $now))
            ->where('is_active = 1');

        return $this->_getReadAdapter()->fetchAll($select);
    }

    /**
     * Deactivate already matched customers before new matching process
     *
     * @return Enterprise_Reminder_Model_Mysql4_Rule
     */
    public function deactiveMatchedCustomers($ruleId)
    {
        $this->_getWriteAdapter()->update(
            $this->getTable('enterprise_reminder/coupon'),
            array('is_active' => '1'),
            array('rule_id = ?' => $ruleId)
        );
        return $this;
    }

    /**
     * Get matched customers
     *
     * @return Enterprise_Reminder_Model_Mysql4_Rule
     */
    public function getMatchedCustomers($select)
    {
        return $this->_getReadAdapter()->fetchAll($select);
    }

    /**
     * Try to associate reminder rule with customer.
     * If customer was added earlier, update is_active column.
     *
     * @return Enterprise_Reminder_Model_Mysql4_Rule
     */
    public function saveMatchedCustomer($ruleId, $customerId, $couponId)
    {
        $data = array(
            'rule_id'       => $ruleId,
            'coupon_id'     => $couponId,
            'customer_id'   => $customerId,
            'associated_at' => $this->formatDate(time()),
            'is_active'     => '1'
        );

        $this->_getWriteAdapter()->insertOnDuplicate(
            $this->getTable('enterprise_reminder/coupon'), $data, array('is_active')
        );

        return $this;
    }

    /**
     *
     * @param $ruleId
     * @return unknown_type
     */
    public function getCustomersForNotification($ruleId)
    {
        $ruleTable = $this->getTable('enterprise_reminder/rule');
        $couponTable = $this->getTable('enterprise_reminder/coupon');
        $templateTable = $this->getTable('enterprise_reminder/template');
        $logTable = $this->getTable('enterprise_reminder/log');
        $customerTable = $this->getTable('customer/entity');

        $subSelect = $this->_getReadAdapter()->select();
        $subSelect->from(array('r'=>$ruleTable),
            'IF((MAX(l.sent_at) IS NULL) OR (TO_DAYS(NOW()) - TO_DAYS(MIN(l.sent_at)) IN (r.schedule) AND TO_DAYS(NOW()) != TO_DAYS(MAX(l.sent_at))),1,0)'
        );

        $subSelect->join(
            array('c'=>$couponTable),
            'c.rule_id=r.rule_id',
            array()
        );

        $subSelect->joinLeft(
            array('l' => $logTable),
            'c.rule_id=l.rule_id AND c.customer_id=l.customer_id',
            array()
        );

        $subSelect->where('r.rule_id=c.rule_id');
        $subSelect->where('c.rule_id=?', $ruleId);
        $subSelect->where('c.is_active=? AND e.entity_id=c.customer_id', '1');
        $subSelect->group('c.customer_id');
        $subSelect->limit('1');

        $mainSelect = $this->_getReadAdapter()->select();
        $mainSelect->from(array('e' => $customerTable), array('entity_id', 'store_id', 'email'));
        $mainSelect->where('IFNULL(?,0)=1', $subSelect);
        $mainSelect->limit('500');

        return $this->_getReadAdapter()->fetchAssoc($mainSelect);
    }

    /**
     * Add notification log
     *
     * @return Enterprise_Reminder_Model_Mysql4_Rule
     */
    public function addNotificationLog($ruleId, $customerId)
    {
        $data = array(
            'rule_id'     => $ruleId,
            'customer_id' => $customerId,
            'sent_at'     => $this->formatDate(time())
        );

        $this->_getWriteAdapter()->insert($this->getTable('enterprise_reminder/log'), $data);
        return $this;
    }
}
