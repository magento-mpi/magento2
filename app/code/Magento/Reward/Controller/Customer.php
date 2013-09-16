<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reward customer controller
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reward_Controller_Customer extends Magento_Core_Controller_Front_Action
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Core_Model_Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Predispatch
     * Check is customer authenticate
     * Check is RP enabled on frontend
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::getSingleton('Magento_Customer_Model_Session')->authenticate($this)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
        if (!$this->_objectManager->get('Magento_Reward_Helper_Data')->isEnabledOnFront()) {
            $this->norouteAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }

    /**
     * Info Action
     */
    public function infoAction()
    {
        $this->_coreRegistry->register('current_reward', $this->_getReward());
        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Customer_Model_Session');
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Reward Points'));
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
                __('You saved the settings.')
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
            /* @var $customer Magento_Customer_Model_Session */
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
                    __('You have been unsubscribed.')
                );
            }
        } catch (Exception $e) {
            $this->_getSession()->addError(__('Failed to unsubscribe'));
        }

        $this->_redirect('*/*/info');
    }

    /**
     * Retrieve customer session model object
     *
     * @return Magento_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('Magento_Customer_Model_Session');
    }

    /**
     * Retrieve customer session model object
     *
     * @return Magento_Customer_Model_Session
     */
    protected function _getCustomer()
    {
        return $this->_getSession()->getCustomer();
    }

    /**
     * Load reward by customer
     *
     * @return Magento_Reward_Model_Reward
     */
    protected function _getReward()
    {
        $reward = Mage::getModel('Magento_Reward_Model_Reward')
            ->setCustomer($this->_getCustomer())
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
            ->loadByCustomer();
        return $reward;
    }
}
