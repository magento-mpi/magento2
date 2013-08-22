<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Reward_Model_Reward_Balance_Validator
{
    /**
     * @var Magento_Reward_Model_RewardFactory
     */
    protected $_modelFactory;

    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @var Magento_Checkout_Model_Session
     */
    protected $_session;

    /**
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Reward_Model_RewardFactory $modelFactory
     * @param Magento_Checkout_Model_Session $session
     */
    public function __construct(
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Reward_Model_RewardFactory $modelFactory,
        Magento_Checkout_Model_Session $session
    ) {
        $this->_storeManager = $storeManager;
        $this->_modelFactory = $modelFactory;
        $this->_session = $session;
    }

    /**
     * Check reward points balance
     *
     * @param Magento_Sales_Model_Order $order
     * @throws Magento_Reward_Model_Reward_Balance_Exception
     */
    public function validate(Magento_Sales_Model_Order $order)
    {
        if ($order->getRewardPointsBalance() > 0) {
            $websiteId = $this->_storeManager->getStore($order->getStoreId())->getWebsiteId();
            /* @var $reward Magento_Reward_Model_Reward */
            $reward = $this->_modelFactory->create();
            $reward->setCustomerId($order->getCustomerId());
            $reward->setWebsiteId($websiteId);
            $reward->loadByCustomer();

            if (($order->getRewardPointsBalance() - $reward->getPointsBalance()) >= 0.0001) {
                $this->_session->setUpdateSection('payment-method');
                $this->_session->setGotoSection('payment');
                throw new Magento_Reward_Model_Reward_Balance_Exception(
                    __('You don\'t have enough reward points to pay for this purchase.')
                );
            }
        }
    }
}
