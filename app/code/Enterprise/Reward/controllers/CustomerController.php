<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @copyright   {copyright}
 * @license     {license_link}
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
        if (!Mage::getSingleton('Mage_Customer_Model_Session')->authenticate($this)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
        if (!Mage::helper('Enterprise_Reward_Helper_Data')->isEnabledOnFront()) {
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
        $this->_initLayoutMessages('Mage_Customer_Model_Session');
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(Mage::helper('Enterprise_Reward_Helper_Data')->__('Reward Points'));
        }
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

        $customer = $this->_getCustomer();
        if ($customer->getId()) {
            $customer->setRewardUpdateNotification($this->getRequest()->getParam('subscribe_updates'))
                ->setRewardWarningNotification($this->getRequest()->getParam('subscribe_warnings'));
            $customer->getResource()->saveAttribute($customer, 'reward_update_notification');
            $customer->getResource()->saveAttribute($customer, 'reward_warning_notification');

            $this->_getSession()->addSuccess(
                $this->__('You saved the settings.')
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
            /* @var $customer Mage_Customer_Model_Session */
            $customer = $this->_getCustomer();
            if ($customer->getId()) {
                if ($notification == 'update') {
                    $customer->setRewardUpdateNotification(false);
                    $customer->getResource()->saveAttribute($customer, 'reward_update_notification');
                } elseif ($notification == 'warning') {
                    $customer->setRewardWarningNotification(false);
                    $customer->getResource()->saveAttribute($customer, 'reward_warning_notification');
                }
                $this->_getSession()->addSuccess(
                    $this->__('You have been unsubscribed.')
                );
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Failed to unsubscribe'));
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
        return Mage::getSingleton('Mage_Customer_Model_Session');
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
        $reward = Mage::getModel('Enterprise_Reward_Model_Reward')
            ->setCustomer($this->_getCustomer())
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
            ->loadByCustomer();
        return $reward;
    }
}
