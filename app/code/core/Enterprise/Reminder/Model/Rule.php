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
     * Perform actions after object load
     */
    protected function _afterLoad()
    {
        Mage_Core_Model_Abstract::_afterLoad();
        $conditionsArr = unserialize($this->getConditionsSerialized());
        if (!empty($conditionsArr) && is_array($conditionsArr)) {
            $this->getConditions()->loadArray($conditionsArr);
        }

        $storeData = $this->_getResource()->getStoreData($this->getId());
        $defaultTemplate = str_replace('/', '_', self::XML_PATH_EMAIL_TEMPLATE);

        foreach($storeData as $data) {
            $template = (empty($data['template_id'])) ? $defaultTemplate : $data['template_id'];
            $this->setData('store_template_'.$data['store_id'], $template);
            $this->setData('store_label_'.$data['store_id'], $data['label']);
            $this->setData('store_description_'.$data['store_id'], $data['description']);
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
}
