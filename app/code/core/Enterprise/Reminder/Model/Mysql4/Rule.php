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
     * Prepare object data for saving
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getActiveFrom()) {
            $date = Mage::app()->getLocale()->date();
            $date->setHour(0)->setMinute(0)->setSecond(0);
            $object->setActiveFrom($date);
        }
        if ($object->getActiveFrom() instanceof Zend_Date) {
            $object->setActiveFrom($object->getActiveFrom()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
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
            $this->_saveStoreTemplates($rule);
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
     * Save store templates
     *
     * @param $rule
     * @return unknown_type
     */
    protected function _saveStoreTemplates($rule)
    {
        $adapter = $this->_getWriteAdapter();
        $templateTabel = $this->getTable('enterprise_reminder/template');
        $adapter->delete($templateTabel, array('rule_id=?'=>$rule->getId()));

        foreach ($rule->getStoreTemplates() as $storeId=>$templateId) {
            if (!$templateId) {
                continue;
            }
            $adapter->insert($templateTabel, array(
                'rule_id'     => $rule->getId(),
                'store_id'    => $storeId,
                'template_id' => $templateId
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
    public function getTemplates($ruleId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('enterprise_reminder/template'), array('store_id', 'template_id'))
            ->where('rule_id=?', $ruleId);
        return $this->_getReadAdapter()->fetchPairs($select);
    }

    /**
     * Get select query result
     *
     * @param   Varien_Db_Select|string $sql
     * @param   array $bindParams array of binded variables
     * @return  int
     */
    public function runConditionSql($sql, $bindParams)
    {
        return $this->_getReadAdapter()->fetchOne($sql, $bindParams);
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
                        $condition = $this->_getReadAdapter()->quoteInto(
                            $field.' '.$sqlOperator.' (?)', $value
                        );
                    }
                } else {
                    $condition = $this->_getReadAdapter()->quoteInto(
                        $field.' '.$sqlOperator.' ?', '%'.$value.'%'
                    );
                }
                break;
            case 'between':
                $condition = $field.' '.sprintf($sqlOperator, $value['start'], $value['end']);
                break;
            default:
                $condition = $this->_getReadAdapter()->quoteInto(
                    $field.' '.$sqlOperator.' ?', $value
                );
                break;
        }
        return $condition;
    }
}
