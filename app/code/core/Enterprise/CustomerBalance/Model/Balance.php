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
 * @package    Enterprise_CustomerBalance
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_CustomerBalance_Model_Balance extends Mage_Core_Model_Abstract
{
	protected $_customer;

    protected function _construct()
    {
        $this->_init('enterprise_customerbalance/balance');
    }

    public function shouldCustomerHaveOneBalance($customer)
    {
        if (0 == $customer->getWebsiteId()) {
            return false;
        }
        return Mage::getSingleton('customer/config_share')->isWebsiteScope();
    }

    public function getAmount()
    {
        return (float)$this->getData('amount');
    }

    public function loadByCustomer()
    {
        $this->_ensureCustomer();
        if ($this->hasWebsiteId()) {
            $websiteId = $this->getWebsiteId();
        }
        else {
            $websiteId = $this->getCustomer()->getWebsiteId();
        }
        $this->getResource()->loadByCustomerAndWebsiteIds($this, $this->getCustomerId(), $websiteId);
        return $this;
    }

    public function setNotifyByEmail($shouldNotify, $storeId = null)
    {
        $this->setData('notify_by_email', $shouldNotify);
        if ($shouldNotify) {
            if (null === $storeId) {
                Mage::throwException(Mage::helper('enterprise_customerbalance')->__('Set Store ID as well.'));
            }
            $this->setStoreId($storeId);
        }
        return $this;

    }

    protected function _beforeSave()
    {
        $this->_ensureCustomer();

        // make sure appropriate website was set. Admin website is disallowed
        if ((!$this->hasWebsiteId()) && $this->shouldCustomerHaveOneBalance($this->getCustomer())) {
            $this->setWebsiteId($this->getCustomer()->getWebsiteId());
        }
        if (0 == $this->getWebsiteId()) {
            Mage::throwException(Mage::helper('enterprise_customerbalance')->__('Website ID must be set.'));
        }

        // check history action
        if (!$this->getId()) {
            $this->loadByCustomer();
            if (!$this->getId()) {
                $this->setHistoryAction(Enterprise_CustomerBalance_Model_Balance_History::ACTION_CREATED);
            }
        }
        if (!$this->hasHistoryAction()) {
            $this->setHistoryAction(Enterprise_CustomerBalance_Model_Balance_History::ACTION_UPDATED);
        }

        // check balance delta and email notification settings
        $delta = $this->_prepareAmountDelta();
        if (0 == $delta) {
            $this->setNotifyByEmail(false);
        }
        if ($this->getNotifyByEmail() && !$this->hasStoreId()) {
            Mage::throwException(Mage::helper('enterprise_customerbalance')->__('In order to send email notification, the Store ID must be set.'));
        }

        return parent::_beforeSave();
    }

    protected function _afterSave()
    {
        parent::_afterSave();

        // save history action
        if (abs($this->getAmountDelta())) {
            $history = Mage::getModel('enterprise_customerbalance/balance_history')
                ->setBalanceModel($this)
                ->save();
        }

        return $this;
    }

    protected function _ensureCustomer()
    {
        if ($this->getCustomer() && $this->getCustomer()->getId()) {
            $this->setCustomerId($this->getCustomer()->getId());
        }
        if (!$this->getCustomerId()) {
            Mage::throwException(Mage::helper('enterprise_customerbalance')->__('Customer ID must be specified.'));
        }
        if (!$this->getCustomer()) {
            $this->setCustomer(Mage::getModel('customer/customer')->load($this->getCustomerId()));
        }
        if (!$this->getCustomer()->getId()) {
            Mage::throwException(Mage::helper('enterprise_customerbalance')->__('Customer is not set or does not exist.'));
        }
    }

    protected function _prepareAmountDelta()
    {
        $result = 0;
        if ($this->hasAmountDelta()) {
            $result = (float)$this->getAmountDelta();
            if ($this->getId()) {
                if (($result < 0) && (($this->getAmount() + $result) < 0)) {
                    $result = -1 * $this->getAmount();
                }
            }
            elseif ($result <= 0) {
                $result = 0;
            }
        }
        $this->setAmountDelta($result);
        if (!$this->getId()) {
            $this->setAmount($result);
        }
        else {
            $this->setAmount($this->getAmount() + $result);
        }
        return $result;
    }
}
