<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reminder Rule data model
 *
 * @method Magento_Reminder_Model_Resource_Rule _getResource()
 * @method Magento_Reminder_Model_Resource_Rule getResource()
 * @method string getName()
 * @method Magento_Reminder_Model_Rule setName(string $value)
 * @method string getDescription()
 * @method Magento_Reminder_Model_Rule setDescription(string $value)
 * @method string getConditionsSerialized()
 * @method Magento_Reminder_Model_Rule setConditionsSerialized(string $value)
 * @method string getConditionSql()
 * @method Magento_Reminder_Model_Rule setConditionSql(string $value)
 * @method int getIsActive()
 * @method Magento_Reminder_Model_Rule setIsActive(int $value)
 * @method int getSalesruleId()
 * @method Magento_Reminder_Model_Rule setSalesruleId(int $value)
 * @method string getSchedule()
 * @method Magento_Reminder_Model_Rule setSchedule(string $value)
 * @method string getDefaultLabel()
 * @method Magento_Reminder_Model_Rule setDefaultLabel(string $value)
 * @method string getDefaultDescription()
 * @method Magento_Reminder_Model_Rule setDefaultDescription(string $value)
 * @method Magento_Reminder_Model_Rule setActiveFrom(string $value)
 * @method Magento_Reminder_Model_Rule setActiveTo(string $value)
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reminder_Model_Rule extends Magento_Rule_Model_Abstract
{
    const XML_PATH_EMAIL_TEMPLATE  = 'magento_reminder_email_template';

    /**
     * Store template data defined per store view, will be used in email templates as variables
     */
    protected $_storeData = array();

    /**
     * Reminder data
     *
     * @var Magento_Reminder_Helper_Data
     */
    protected $_reminderData = null;

    /**
     * @param Magento_Reminder_Helper_Data $reminderData
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Reminder_Model_Resource_Rule $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Reminder_Helper_Data $reminderData,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Reminder_Model_Resource_Rule $resource,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_reminderData = $reminderData;
        parent::__construct($formFactory, $context, $registry, $locale, $resource, $resourceCollection, $data);
    }

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento_Reminder_Model_Resource_Rule');
    }

    /**
     * Set template, label and description data per store
     *
     * @return Magento_Reminder_Model_Rule
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        $storeData = $this->_getResource()->getStoreData($this->getId());
        $defaultTemplate = self::XML_PATH_EMAIL_TEMPLATE;

        foreach ($storeData as $data) {
            $template = (empty($data['template_id'])) ? $defaultTemplate : $data['template_id'];
            $this->setData('store_template_' . $data['store_id'], $template);
            $this->setData('store_label_' . $data['store_id'], $data['label']);
            $this->setData('store_description_' . $data['store_id'], $data['description']);
        }

        return $this;
    }

    /**
     * Set aggregated conditions SQL and reset sales rule Id if applicable
     *
     * @return Magento_Reminder_Model_Rule
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
        return $this;
    }

    /**
     * Getter for rule combine conditions instance
     *
     * @return Magento_Reminder_Model_Rule_Condition_Combine
     */
    public function getConditionsInstance()
    {
        return Mage::getModel('Magento_Reminder_Model_Rule_Condition_Combine_Root');
    }

    /**
     * Getter for rule actions collection instance
     *
     * @return Magento_Rule_Model_Action_Collection
     */
    public function getActionsInstance()
    {
        return Mage::getModel('Magento_Rule_Model_Action_Collection');
    }

    /**
     * Send reminder emails
     *
     * @return Magento_Reminder_Model_Rule
     */
    public function sendReminderEmails()
    {
        /** @var $mail Magento_Core_Model_Email_Template */
        $mail = Mage::getModel('Magento_Core_Model_Email_Template');

        /* @var $translate Magento_Core_Model_Translate */
        $translate = Mage::getSingleton('Magento_Core_Model_Translate');
        $translate->setTranslateInline(false);

        $identity = $this->_reminderData->getEmailIdentity();

        $this->_matchCustomers();
        $limit = $this->_reminderData->getOneRunLimit();

        $recipients = $this->_getResource()->getCustomersForNotification($limit, $this->getRuleId());

        foreach ($recipients as $recipient) {
            /* @var $customer Magento_Customer_Model_Customer */
            $customer = Mage::getModel('Magento_Customer_Model_Customer')->load($recipient['customer_id']);
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

            /* @var $coupon Magento_SalesRule_Model_Coupon */
            $coupon = Mage::getModel('Magento_SalesRule_Model_Coupon')->load($recipient['coupon_id']);

            $templateVars = array(
                'store'          => $store,
                'coupon'         => $coupon,
                'customer'       => $customer,
                'promotion_name' => $storeData['label'],
                'promotion_description' => $storeData['description']
            );

            $mail->setDesignConfig(array(
                'area' => Magento_Core_Model_App_Area::AREA_FRONTEND,
                'store' => $store->getId()
            ));
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
     * Match customers for current rule and assign coupons
     *
     * @return Magento_Reminder_Model_Observer
     */
    protected function _matchCustomers()
    {
        $threshold   = $this->_reminderData->getSendFailureThreshold();
        $currentDate = Mage::getModel('Magento_Core_Model_Date')->date('Y-m-d');
        $rules       = $this->getCollection()->addDateFilter($currentDate)->addIsActiveFilter(1);

        if ($this->getRuleId()) {
            $rules->addRuleFilter($this->getRuleId());
        }

        foreach ($rules as $rule) {
            $this->_getResource()->deactivateMatchedCustomers($rule->getId());

            if ($rule->getSalesruleId()) {
                /* @var $salesRule Magento_SalesRule_Model_Rule */
                $salesRule = Mage::getSingleton('Magento_SalesRule_Model_Rule')->load($rule->getSalesruleId());
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
     * Retrieve store template data
     *
     * @param int $ruleId
     * @param int $storeId
     *
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

    /**
     * Detaches Sales Rule from all Email Remainder Rules that uses it
     *
     * @param int $salesRuleId
     * @return Magento_Reminder_Model_Rule
     */
    public function detachSalesRule($salesRuleId)
    {
        $this->getResource()->detachSalesRule($salesRuleId);
        return $this;
    }

    /**
     * Retrieve active from date.
     * Implemented for backwards compatibility with old property called "active_from"
     *
     * @return string
     */
    public function getActiveFrom()
    {
        return $this->getData('from_date');
    }

    /**
     * Retrieve active to date.
     * Implemented for backwards compatibility with old property called "active_to"
     *
     * @return string
     */
    public function getActiveTo()
    {
        return $this->getData('to_date');
    }
}
