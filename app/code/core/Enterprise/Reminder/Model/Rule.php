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
class Enterprise_Reminder_Model_Rule extends Enterprise_Enterprise_Model_Rule_Rule
{
    const XML_PATH_EMAIL_TEMPLATE  = 'enterprise_reminder/email/template';

    /**
     * Intialize model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('enterprise_reminder/rule');
    }

    /**
     * Return conditions instance
     *
     * @return Enterprise_Reminder_Model_Rule_Condition_Combine
     */
    public function getConditionsInstance()
    {
        return Mage::getModel('enterprise_reminder/rule_condition_combine_root');
    }

    /**
     * Get rule associated website ids
     *
     * @return array
     */
    public function getWebsiteIds()
    {
        if (!$this->hasData('website_ids')) {
            $this->setData('website_ids', $this->_getResource()->getWebsiteIds($this->getId()));
        }
        return $this->_getData('website_ids');
    }

    /**
     * Get existing templates
     *
     * @return array
     */
    public function getTemplates()
    {
        $templates = $this->_getResource()->getTemplates($this->getId());
        foreach($templates as $store => $template) {
            $this->setData('store_template_'.$store, $template);
        }
    }

    /**
     * Perform actions after object load
     */
    protected function _afterLoad()
    {
        Mage_Core_Model_Abstract::_afterLoad();
        $conditionsArr = unserialize($this->getConditionsSerialized());
        if (!empty($conditionsArr) && is_array($conditionsArr)) {
            $this->getConditions()->loadArray($conditionsArr);
        }
        return $this;
    }

    /**
     * Perform actions before object save.
     */
    protected function _beforeSave()
    {
        $customer = new Zend_Db_Expr(':customer_id');
        $website = new Zend_Db_Expr(':website_id');

        $this->setConditionSql(
            $this->getConditions()->getConditionsSql($customer, $website)
        );

        parent::_beforeSave();
    }

    /**
     * Live website ids data as is
     *
     * @return Enterprise_Reminder_Model_Rule
     */
    protected function _prepareWebsiteIds()
    {
        return $this;
    }

    /**
     * Get list of all models which are used in rule conditions
     *
     * @param  null | Mage_Rule_Model_Condition_Combine $conditions
     * @return array
     */
    public function getConditionModels($conditions = null)
    {
        $models = array();

        if (is_null($conditions)) {
            $conditions = $this->getConditions();
        }

        $models[] = $conditions->getType();
        $childConditions = $conditions->getConditions();
        if ($childConditions) {
            if (is_array($childConditions)) {
                foreach ($childConditions as $child) {
                    $models = array_merge($models, $this->getConditionModels($child));
                }
            } else {
                $models = array_merge($models, $this->getConditionModels($childConditions));
            }
        }

        return $models;
    }
}
