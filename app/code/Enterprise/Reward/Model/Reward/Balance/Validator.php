<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Reward_Model_Reward_Balance_Validator
{
    /**
     * @var Enterprise_Reward_Model_RewardFactory
     */
    protected $_modelFactory;

    /**
     * @var Mage_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @var Mage_Checkout_Model_Session
     */
    protected $_session;

    /**
     * @param Mage_Core_Model_StoreManager $storeManager
     * @param Enterprise_Reward_Model_RewardFactory $modelFactory
     * @param Mage_Checkout_Model_Session $session
     */
    public function __construct(
        Mage_Core_Model_StoreManager $storeManager,
        Enterprise_Reward_Model_RewardFactory $modelFactory,
        Mage_Checkout_Model_Session $session
    ) {
        $this->_storeManager = $storeManager;
        $this->_modelFactory = $modelFactory;
        $this->_session = $session;
    }

    /**
     * Check reward points balance
     *
     * @param Mage_Sales_Model_Order $order
     * @throws Enterprise_Reward_Model_Reward_Balance_Exception
     */
    public function validate(Mage_Sales_Model_Order $order)
    {
        if ($order->getRewardPointsBalance() > 0) {
            $websiteId = $this->_storeManager->getStore($order->getStoreId())->getWebsiteId();
            /* @var $reward Enterprise_Reward_Model_Reward */
            $reward = $this->_modelFactory->create();
            $reward->setCustomerId($order->getCustomerId());
            $reward->setWebsiteId($websiteId);
            $reward->loadByCustomer();

            if (($order->getRewardPointsBalance() - $reward->getPointsBalance()) >= 0.0001) {
                $this->_session->setUpdateSection('payment-method');
                $this->_session->setGotoSection('payment');
                throw new Enterprise_Reward_Model_Reward_Balance_Exception(
                    __('You don\'t have enough reward points to pay for this purchase.')
                );
            }
        }
    }
}
