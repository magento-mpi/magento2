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
 * @package     Enterprise_Reward
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Reward customer controller
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_CustomerController extends Mage_Core_Controller_Front_Action
{
    /**
     * Predispatch
     * Check is customer authenticate
     * Check is RP enabled on frontend
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
        if (!Mage::helper('enterprise_reward')->isEnabledOnFront()
            || !Mage::helper('enterprise_reward')->getHasRates()) {
            $this->norouteAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }

    /**
     * Info Action
     */
    public function infoAction()
    {
        Mage::register('current_reward', $this->_getReward());
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    /**
     * Save settings
     */
    public function saveSettingsAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/info');
        }

        $reward = $this->_getReward();
        if ($reward->getId()) {
            $reward->changeBalanceUpdateNotification($this->getRequest()->getParam('subscribe_updates'))
                ->changeBalanceWarningNotification($this->getRequest()->getParam('subscribe_warnings'));

            $this->_getSession()->addSuccess(
                $this->__('Settings were successfully saved.')
            );
        }
        $this->_redirect('*/*/info');
    }

    /**
     * Unsubscribe customer from update/warning balance notifications
     */
    public function unsubscribeAction()
    {
        $notification = $this->getRequest()->getParam('notification');
        if (!in_array($notification, array('update','warning'))) {
            $this->_forward('noroute');
        }

        try {
            /* @var $reward Enterprise_Reward_Model_Reward */
            $reward = $this->_getReward();
            if (!$reward->getId()) {
                Mage::throwException($this->__('Reward not found for customer.'));
            }

            if ($notification == 'update') {
                $reward->changeBalanceUpdateNotification(false);
            } elseif ($notification == 'warning') {
                $reward->changeBalanceWarningNotification(false);
            }

            $this->_getSession()->addSuccess(
                $this->__('You have been successfully unsubscribed.')
            );
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Unsubscribtion failed.'));
        }

        $this->_redirect('*/*/info');
    }

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomer()
    {
        return $this->_getSession()->getCustomer();
    }

    /**
     * Load reward by customer
     *
     * @return Enterprise_Reward_Model_Reward
     */
    protected function _getReward()
    {
        $reward = Mage::getModel('enterprise_reward/reward')
            ->setCustomer($this->_getCustomer())
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
            ->loadByCustomer();
        return $reward;
    }
}
