<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reminder rules processing model
 *
 * @method Enterprise_Reminder_Model_Resource_Rule _getResource()
 * @method Enterprise_Reminder_Model_Resource_Rule getResource()
 * @method string getName()
 * @method Enterprise_Reminder_Model_Rule setName(string $value)
 * @method string getDescription()
 * @method Enterprise_Reminder_Model_Rule setDescription(string $value)
 * @method string getConditionsSerialized()
 * @method Enterprise_Reminder_Model_Rule setConditionsSerialized(string $value)
 * @method string getConditionSql()
 * @method Enterprise_Reminder_Model_Rule setConditionSql(string $value)
 * @method int getIsActive()
 * @method Enterprise_Reminder_Model_Rule setIsActive(int $value)
 * @method int getSalesruleId()
 * @method Enterprise_Reminder_Model_Rule setSalesruleId(int $value)
 * @method string getSchedule()
 * @method Enterprise_Reminder_Model_Rule setSchedule(string $value)
 * @method string getDefaultLabel()
 * @method Enterprise_Reminder_Model_Rule setDefaultLabel(string $value)
 * @method string getDefaultDescription()
 * @method Enterprise_Reminder_Model_Rule setDefaultDescription(string $value)
 * @method string getActiveFrom()
 * @method Enterprise_Reminder_Model_Rule setActiveFrom(string $value)
 * @method string getActiveTo()
 * @method Enterprise_Reminder_Model_Rule setActiveTo(string $value)
 *
 * @category    Enterprise
 * @package     Enterprise_Reminder
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reminder_Model_Rule extends Mage_Rule_Model_Rule
{
    const XML_PATH_EMAIL_TEMPLATE  = 'enterprise_reminder_email_template';

    /**
     * Contains data defined per store view, will be used in email templates as variables
     */
    protected $_storeData = array();

    /**
     * Intialize model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Enterprise_Reminder_Model_Resource_Rule');
    }

    /**
     * Perform actions after object load
     *
     * @return Enterprise_Reminder_Model_Rule
     */
    protected function _afterLoad()
    {
        Mage_Core_Model_Abstract::_afterLoad();
        $conditionsArr = unserialize($this->getConditionsSerialized());
        if (!empty($conditionsArr) && is_array($conditionsArr)) {
            $this->getConditions()->loadArray($conditionsArr);
        }

        $storeData = $this->_getResource()->getStoreData($this->getId());
        $defaultTemplate = self::XML_PATH_EMAIL_TEMPLATE;

        foreach($storeData as $data) {
            $template = (empty($data['template_id'])) ? $defaultTemplate : $data['template_id'];
            $this->setData('store_template_' . $data['store_id'], $template);
            $this->setData('store_label_' . $data['store_id'], $data['label']);
            $this->setData('store_description_' . $data['store_id'], $data['description']);
        }

        return $this;
    }

    /**
     * Perform actions before object save.
     */
    protected function _beforeSave()
    {
        $this->setConditionSql(
            $this->getConditions()->getConditionsSql(null, new Zend_Db_Expr(':website_id'))
        );

        if (!$this->getSalesruleId()) {
            $this->setSalesruleId(null);
        }
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
        return Mage::getModel('Enterprise_Reminder_Model_Rule_Condition_Combine_Root');
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
     * Send reminder emails
     *
     * @return Enterprise_Reminder_Model_Rule
     */
    public function sendReminderEmails()
    {
        $mail = Mage::getModel('Mage_Core_Model_Email_Template');

        /* @var $translate Mage_Core_Model_Translate */
        $translate = Mage::getSingleton('Mage_Core_Model_Translate');
        $translate->setTranslateInline(false);

        $identity = Mage::helper('Enterprise_Reminder_Helper_Data')->getEmailIdentity();

        $this->_matchCustomers();
        $limit = Mage::helper('Enterprise_Reminder_Helper_Data')->getOneRunLimit();

        $recipients = $this->_getResource()->getCustomersForNotification($limit, $this->getRuleId());

        foreach ($recipients as $recipient) {

            /* @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::getModel('Mage_Customer_Model_Customer')->load($recipient['customer_id']);
            if (!$customer || !$customer->getId()) {
                continue;
            }

            if ($customer->getStoreId()) {
                $store = $customer->getStore();
            } else {
                $store = Mage::app()->getWebsite($customer->getWebsiteId())->getDefaultStore();
            }

            $storeData = $this->getStoreData($recipient['rule_id'], $store->getId());
            if (!$storeData) {
                continue;
            }

            /* @var $coupon Mage_SalesRule_Model_Coupon */
            $coupon = Mage::getModel('Mage_SalesRule_Model_Coupon')->load($recipient['coupon_id']);

            $templateVars = array(
                'store' => $store,
                'customer' => $customer,
                'promotion_name' => $storeData['label'],
                'promotion_description' => $storeData['description'],
                'coupon' => $coupon
            );

            $mail->setDesignConfig(array('area' => 'frontend', 'store' => $store->getId()));
            $mail->sendTransactional($storeData['template_id'], $identity,
                $customer->getEmail(), null, $templateVars, $store->getId()
            );

            if ($mail->getSentSuccess()) {
                $this->_getResource()->addNotificationLog($recipient['rule_id'], $customer->getId());
            } else {
                $this->_getResource()->updateFailedEmailsCounter($recipient['rule_id'], $customer->getId());
            }
        }

        $translate->setTranslateInline(true);
        return $this;
    }

    /**
     * Match customers and assign coupons
     *
     * @return Enterprise_Reminder_Model_Observer
     */
    protected function _matchCustomers()
    {
        $threshold = Mage::helper('Enterprise_Reminder_Helper_Data')->getSendFailureThreshold();

        $currentDate = Mage::getModel('Mage_Core_Model_Date')->date('Y-m-d');
        $rules = $this->getCollection()->addDateFilter($currentDate)
            ->addIsActiveFilter(1);

        if ($ruleId = $this->getRuleId()) {
            $rules->addRuleFilter($ruleId);
        }

        foreach ($rules as $rule) {
            $this->_getResource()->deactivateMatchedCustomers($rule->getId());

            if ($rule->getSalesruleId()) {
                /* @var $salesRule Mage_SalesRule_Model_Rule */
                $salesRule = Mage::getSingleton('Mage_SalesRule_Model_Rule')->load($rule->getSalesruleId());
                $websiteIds = array_intersect($rule->getWebsiteIds(), $salesRule->getWebsiteIds());
            } else {
                $salesRule = null;
                $websiteIds = $rule->getWebsiteIds();
            }

            foreach ($websiteIds as $websiteId) {
                $this->_getResource()->saveMatchedCustomers($rule, $salesRule, $websiteId, $threshold);
            }
        }
        return $this;
    }

    /**
     * Return store data
     *
     * @param int $ruleId
     * @param int $storeId
     * @return array|false
     */
    public function getStoreData($ruleId, $storeId)
    {
        if (!isset($this->_storeData[$ruleId][$storeId])) {
            if ($data = $this->_getResource()->getStoreTemplateData($ruleId, $storeId)) {
                if (empty($data['template_id'])) {
                    $data['template_id'] = self::XML_PATH_EMAIL_TEMPLATE;
                }
                $this->_storeData[$ruleId][$storeId] = $data;
            }
            else {
                return false;
            }
        }
        return $this->_storeData[$ruleId][$storeId];
    }
}
