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
 * Reminder rules observer model
 */
class Enterprise_Reminder_Model_Observer
{
    const CRON_MINUTELY = 'I';
    const CRON_HOURLY   = 'H';
    const CRON_DAILY    = 'D';

    const XML_PATH_EMAIL_LIMIT  = 'enterprise_reminder/general/limit';
    const XML_PATH_EMAIL_IDENTITY  = 'enterprise_reminder/general/identity';

    /**
     * Contains data defined per store view, will be used in email templates as variables
     */
    protected $_storeData = array();

    /**
     * Get reminder rule resource model
     *
     * @return Enterprise_Reminder_Model_Mysql4_Rule
     */
    public function getResource()
    {
        return Mage::getResourceSingleton('enterprise_reminder/rule');
    }

    /**
     * Get reminder rules collection
     *
     * @return Enterprise_Reminder_Model_Mysql4_Rule_Collection
     */
    public function getRulesCollection()
    {
        $currentDate = Mage::getModel('core/date')->date('Y-m-d');
        $collection = Mage::getResourceModel('enterprise_reminder/rule_collection')
            ->addDateFilter($currentDate)
            ->addIsActiveFilter(1);

        return $collection;
    }

    /**
     * Match customers and assign coupons
     *
     * @return Enterprise_Reminder_Model_Observer
     */
    public function matchCustomers()
    {
        foreach ($this->getRulesCollection() as $rule) {
            $this->getResource()->deactivateMatchedCustomers($rule->getId());

            /* @var $salesRule Mage_SalesRule_Model_Rule */
            $salesRule = Mage::getSingleton('salesrule/rule')->load($rule->getSalesruleId());
            if (!$salesRule || !$salesRule->getId()) {
                continue;
            }

            $websiteIds = array_intersect($rule->getWebsiteIds(), $salesRule->getWebsiteIds());
            foreach ($websiteIds as $websiteId) {
                $this->getResource()->saveMatchedCustomers($rule, $salesRule, $websiteId);
            }
        }
        return $this;
    }

    /**
     * Send scheduled notifications
     *
     * @return Enterprise_Reminder_Model_Observer
     */
    public function scheduledNotification()
    {
        if (!Mage::helper('enterprise_reminder')->isEnabled()) {
            return $this;
        }

        $this->matchCustomers();

        $mail = Mage::getModel('core/email_template');
        $translate = Mage::getSingleton('core/translate');

        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $limit = $this->getOneRunLimit();
        $recipients = $this->getResource()->getCustomersForNotification($limit);

        foreach ($recipients as $recipient) {

            /* @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::getModel('customer/customer')->load($recipient['customer_id']);
            if (!$customer || !$customer->getId()) {
                continue;
            }

            $storeId = $customer->getStoreId();

            $storeData = $this->getStoreData($recipient['rule_id'], $storeId);
            if (!$storeData) {
                continue;
            }

            /* @var $coupon Mage_SalesRule_Model_Coupon */
            $coupon = Mage::getModel('salesrule/coupon')->load($recipient['coupon_id']);

            $templateVars = array(
                'store' => Mage::app()->getStore($storeId),
                'customer' => $customer,
                'promotion_name' => $storeData['label'],
                'promotion_description' => $storeData['description'],
                'coupon' => $coupon
            );

            $mail->setDesignConfig(array('area'=>'frontend', 'store'=>$storeId));
            $mail->sendTransactional($storeData['template_id'], $this->getEmailIdentity(),
                $customer->getEmail(), null, $templateVars, $storeId
            );

            if ($mail->getSentSuccess()) {
                $this->getResource()->addNotificationLog($recipient['rule_id'], $customer->getId());
            }
        }

        $translate->setTranslateInline(true);
        return $this;
    }

    /**
     * Include auto coupon type
     *
     * @param   Varien_Event_Observer $observer
     * @return  Enterprise_Reminder_Model_Observer
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
     * @param   Varien_Event_Observer $observer
     * @return  Enterprise_Reminder_Model_Observer
     */
    public function updatePromoQuoteTabMainForm($observer)
    {
        $form = $observer->getEvent()->getForm();
        if (!$form) {
            return $this;
        }
        if ($fieldset = $form->getElements()->searchById('base_fieldset')) {
            if ($couponTypeFiled = $fieldset->getElements()->searchById('coupon_type')) {
                $couponTypeFiled->setNote(
                    Mage::helper('enterprise_reminder')->__('Coupons can be auto-generated by reminder promotion rules.'));
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
        return array(
            self::CRON_MINUTELY => Mage::helper('cron')->__('Minutely'),
            self::CRON_HOURLY   => Mage::helper('cron')->__('Hourly'),
            self::CRON_DAILY    => Mage::helper('cron')->__('Daily')
        );
    }

    /**
     * Return array of cron valid munutes
     *
     * @return array
     */
    public function getCronMinutes()
    {
        return array(
            5  => Mage::helper('cron')->__('5 minutes'),
            10 => Mage::helper('cron')->__('10 minutes'),
            15 => Mage::helper('cron')->__('15 minutes'),
            20 => Mage::helper('cron')->__('20 minutes'),
            30 => Mage::helper('cron')->__('30 minutes')
        );
    }

    /**
     * Return maximum letters that can be send per one run
     *
     * @return int
     */
    public function getOneRunLimit()
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_EMAIL_LIMIT);
    }

    /**
     * Return email sender information
     *
     * @return string
     */
    public function getEmailIdentity()
    {
        return (string)Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY);
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
            if ($data = $this->getResource()->getStoreTemplateData($ruleId, $storeId)) {
                if (empty($data['template_id'])) {
                    $data['template_id'] = Enterprise_Reminder_Model_Rule::XML_PATH_EMAIL_TEMPLATE;
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
