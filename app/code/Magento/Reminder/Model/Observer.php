<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reminder\Model;

use Magento\Framework\Event\Observer as EventObserver;

/**
 * Reminder rules observer model
 */
class Observer
{
    const CRON_MINUTELY = 'I';

    const CRON_HOURLY = 'H';

    const CRON_DAILY = 'D';

    /**
     * Reminder data
     *
     * @var \Magento\Reminder\Helper\Data
     */
    protected $_reminderData = null;

    /**
     * Remainder Rule Factory
     *
     * @var \Magento\Reminder\Model\RuleFactory
     */
    protected $_ruleFactory;

    /**
     * @param \Magento\Reminder\Helper\Data $reminderData
     * @param \Magento\Reminder\Model\RuleFactory $ruleFactory
     */
    public function __construct(
        \Magento\Reminder\Helper\Data $reminderData,
        \Magento\Reminder\Model\RuleFactory $ruleFactory
    ) {
        $this->_reminderData = $reminderData;
        $this->_ruleFactory = $ruleFactory;
    }

    /**
     * Include auto coupon type
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function getCouponTypes($observer)
    {
        if ($transport = $observer->getEvent()->getTransport()) {
            $transport->setIsCouponTypeAutoVisible(true);
        }
        return $this;
    }

    /**
     * Add custom comment after coupon type field
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function updatePromoQuoteTabMainForm($observer)
    {
        $form = $observer->getEvent()->getForm();
        if (!$form) {
            return $this;
        }
        if ($fieldset = $form->getElements()->searchById('base_fieldset')) {
            if ($couponTypeFiled = $fieldset->getElements()->searchById('coupon_type')) {
                $couponTypeFiled->setNote(__('You can create auto-generated coupons using reminder promotion rules.'));
            }
        }
        return $this;
    }

    /**
     * Return array of cron frequency types
     *
     * @return array
     */
    public function getCronFrequencyTypes()
    {
        return [
            self::CRON_MINUTELY => __('Minute Intervals'),
            self::CRON_HOURLY => __('Hourly'),
            self::CRON_DAILY => __('Daily')
        ];
    }

    /**
     * Return array of cron valid munutes
     *
     * @return array
     */
    public function getCronMinutes()
    {
        return [
            5 => __('5 minutes'),
            10 => __('10 minutes'),
            15 => __('15 minutes'),
            20 => __('20 minutes'),
            30 => __('30 minutes')
        ];
    }

    /**
     * Send scheduled notifications
     *
     * @return $this|void
     */
    public function scheduledNotification()
    {
        if ($this->_reminderData->isEnabled()) {
            $this->_ruleFactory->create()->sendReminderEmails();
            return $this;
        }
    }

    /**
     * Checks whether Sales Rule can be used in Email Remainder Rules and if it cant -
     * detaches it from Email Remainder Rules
     *
     * @param EventObserver $observer
     *
     * @return void
     */
    public function detachUnsupportedSalesRule($observer)
    {
        $rule = $observer->getRule();
        $couponType = $rule->getCouponType();
        $autoGeneration = $rule->getUseAutoGeneration();

        if ($couponType == \Magento\SalesRule\Model\Rule::COUPON_TYPE_SPECIFIC && !empty($autoGeneration)) {
            $model = $this->_ruleFactory->create();
            $ruleId = $rule->getId();
            $model->detachSalesRule($ruleId);
        }
    }

    /**
     * Adds filter to collection which excludes all rules that can't be used in Email Remainder Rules
     *
     * @param EventObserver $observer
     *
     * @return void
     */
    public function addSalesRuleFilter($observer)
    {
        $collection = $observer->getCollection();
        $collection->addAllowedSalesRulesFilter();
    }

    /**
     * Adds notice to "Use Auto Generation" checkbox
     *
     * @param EventObserver $observer
     *
     * @return void
     */
    public function addUseAutoGenerationNotice($observer)
    {
        $form = $observer->getForm();
        $checkbox = $form->getElement('use_auto_generation');
        $checkbox->setNote(
            $checkbox->getNote() . '<br />' . __(
                '<b>Important</b>: If you select "Use Auto Generation", this rule will no longer be used in any automated email reminder rules for abandoned carts'
            )
        );
    }
}
