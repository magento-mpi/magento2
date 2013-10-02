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
 * @method \Magento\Reminder\Model\Resource\Rule _getResource()
 * @method \Magento\Reminder\Model\Resource\Rule getResource()
 * @method string getName()
 * @method \Magento\Reminder\Model\Rule setName(string $value)
 * @method string getDescription()
 * @method \Magento\Reminder\Model\Rule setDescription(string $value)
 * @method string getConditionsSerialized()
 * @method \Magento\Reminder\Model\Rule setConditionsSerialized(string $value)
 * @method string getConditionSql()
 * @method \Magento\Reminder\Model\Rule setConditionSql(string $value)
 * @method int getIsActive()
 * @method \Magento\Reminder\Model\Rule setIsActive(int $value)
 * @method int getSalesruleId()
 * @method \Magento\Reminder\Model\Rule setSalesruleId(int $value)
 * @method string getSchedule()
 * @method \Magento\Reminder\Model\Rule setSchedule(string $value)
 * @method string getDefaultLabel()
 * @method \Magento\Reminder\Model\Rule setDefaultLabel(string $value)
 * @method string getDefaultDescription()
 * @method \Magento\Reminder\Model\Rule setDefaultDescription(string $value)
 * @method \Magento\Reminder\Model\Rule setActiveFrom(string $value)
 * @method \Magento\Reminder\Model\Rule setActiveTo(string $value)
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reminder\Model;

class Rule extends \Magento\Rule\Model\AbstractModel
{
    const XML_PATH_EMAIL_TEMPLATE  = 'magento_reminder_email_template';

    /**
     * Store template data defined per store view, will be used in email templates as variables
     */
    protected $_storeData = array();

    /**
     * Reminder data
     *
     * @var \Magento\Reminder\Helper\Data
     */
    protected $_reminderData = null;

    /**
     * @var Rule\Condition\Combine\RootFactory
     */
    protected $rootFactory;

    /**
     * @var \Magento\Rule\Model\Action\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Core\Model\Email\Template
     */
    protected $emailTemplateFactory;

    /**
     * @var \Magento\Core\Model\Translate
     */
    protected $translate;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Core\Model\StoreManager
     */
    protected $storeManger;

    /**
     * @var \Magento\SalesRule\Model\CouponFactory
     */
    protected $couponFactory;

    /**
     * @var \Magento\Core\Model\DateFactory
     */
    protected $dateFactory;

    /**
     * @var \Magento\SalesRule\Model\Rule
     */
    protected $salesRule;
    /**
     * @param \Magento\Reminder\Model\Rule\Condition\Combine\RootFactory $rootFactory
     * @param \Magento\Rule\Model\Action\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\Email\Template $emailTemplateFactory
     * @param \Magento\Core\Model\Translate $translate
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Core\Model\StoreManager $storeManger
     * @param \Magento\SalesRule\Model\CouponFactory $couponFactory
     * @param \Magento\Core\Model\DateFactory $dateFactory
     * @param \Magento\SalesRule\Model\Rule $salesRule
     * @param \Magento\Reminder\Helper\Data $reminderData
     * @param \Magento\Data\Form\Factory $formFactory
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Reminder\Model\Resource\Rule $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Reminder\Model\Rule\Condition\Combine\RootFactory $rootFactory,
        \Magento\Rule\Model\Action\CollectionFactory $collectionFactory,
        \Magento\Core\Model\Email\Template $emailTemplateFactory,
        \Magento\Core\Model\Translate $translate,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Core\Model\StoreManager $storeManger,
        \Magento\SalesRule\Model\CouponFactory $couponFactory,
        \Magento\Core\Model\DateFactory $dateFactory,
        \Magento\SalesRule\Model\Rule $salesRule,
        \Magento\Reminder\Helper\Data $reminderData,
        \Magento\Data\Form\Factory $formFactory,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Reminder\Model\Resource\Rule $resource,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->rootFactory = $rootFactory;
        $this->collectionFactory = $collectionFactory;
        $this->emailTemplateFactory = $emailTemplateFactory;
        $this->translate = $translate;
        $this->customerFactory = $customerFactory;
        $this->storeManger = $storeManger;
        $this->couponFactory = $couponFactory;
        $this->dateFactory = $dateFactory;
        $this->salesRule = $salesRule;
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
        $this->_init('Magento\Reminder\Model\Resource\Rule');
    }

    /**
     * Set template, label and description data per store
     *
     * @return \Magento\Reminder\Model\Rule
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
     * @return \Magento\Reminder\Model\Rule
     */
    protected function _beforeSave()
    {
        $this->setConditionSql(
            $this->getConditions()->getConditionsSql(null, new \Zend_Db_Expr(':website_id'))
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
     * @return \Magento\Reminder\Model\Rule\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->rootFactory->create();
    }

    /**
     * Getter for rule actions collection instance
     *
     * @return \Magento\Rule\Model\Action\Collection
     */
    public function getActionsInstance()
    {
        return $this->collectionFactory->create();
    }

    /**
     * Send reminder emails
     *
     * @return \Magento\Reminder\Model\Rule
     */
    public function sendReminderEmails()
    {
        /** @var $mail \Magento\Core\Model\Email\Template */
        $mail = $this->emailTemplateFactory->create();

        $this->translate->setTranslateInline(false);

        $identity = $this->_reminderData->getEmailIdentity();

        $this->_matchCustomers();
        $limit = $this->_reminderData->getOneRunLimit();

        $recipients = $this->_getResource()->getCustomersForNotification($limit, $this->getRuleId());

        foreach ($recipients as $recipient) {
            /* @var $customer \Magento\Customer\Model\Customer */
            $customer = $this->customerFactory->create()->load($recipient['customer_id']);
            if (!$customer || !$customer->getId()) {
                continue;
            }

            if ($customer->getStoreId()) {
                $store = $customer->getStore();
            } else {
                $store = $this->storeManger->getWebsite($customer->getWebsiteId())->getDefaultStore();
            }

            $storeData = $this->getStoreData($recipient['rule_id'], $store->getId());
            if (!$storeData) {
                continue;
            }

            /* @var $coupon \Magento\SalesRule\Model\Coupon */
            $coupon = $this->couponFactory->create()->load($recipient['coupon_id']);

            $templateVars = array(
                'store'          => $store,
                'coupon'         => $coupon,
                'customer'       => $customer,
                'promotion_name' => $storeData['label'],
                'promotion_description' => $storeData['description']
            );

            $mail->setDesignConfig(array(
                'area' => \Magento\Core\Model\App\Area::AREA_FRONTEND,
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
        $this->translate->setTranslateInline(true);

        return $this;
    }

    /**
     * Match customers for current rule and assign coupons
     *
     * @return \Magento\Reminder\Model\Observer
     */
    protected function _matchCustomers()
    {
        $threshold   = $this->_reminderData->getSendFailureThreshold();
        $currentDate = $this->dateFactory->create()->date('Y-m-d');
        $rules       = $this->getCollection()->addDateFilter($currentDate)->addIsActiveFilter(1);

        if ($this->getRuleId()) {
            $rules->addRuleFilter($this->getRuleId());
        }

        foreach ($rules as $rule) {
            $this->_getResource()->deactivateMatchedCustomers($rule->getId());

            if ($rule->getSalesruleId()) {
                /* @var $salesRule \Magento\SalesRule\Model\Rule */
                $salesRule = $this->salesRule->load($rule->getSalesruleId());
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
     * @return \Magento\Reminder\Model\Rule
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
