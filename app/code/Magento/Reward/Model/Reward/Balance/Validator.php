<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Reward\Balance;

class Validator
{
    /**
     * @var Magento_Reward_Model_RewardFactory
     */
    protected $_modelFactory;

    /**
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_session;

    /**
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param Magento_Reward_Model_RewardFactory $modelFactory
     * @param \Magento\Checkout\Model\Session $session
     */
    public function __construct(
        \Magento\Core\Model\StoreManager $storeManager,
        Magento_Reward_Model_RewardFactory $modelFactory,
        \Magento\Checkout\Model\Session $session
    ) {
        $this->_storeManager = $storeManager;
        $this->_modelFactory = $modelFactory;
        $this->_session = $session;
    }

    /**
     * Check reward points balance
     *
     * @param \Magento\Sales\Model\Order $order
     * @throws \Magento\Reward\Model\Reward\Balance\Exception
     */
    public function validate(\Magento\Sales\Model\Order $order)
    {
        if ($order->getRewardPointsBalance() > 0) {
            $websiteId = $this->_storeManager->getStore($order->getStoreId())->getWebsiteId();
            /* @var $reward \Magento\Reward\Model\Reward */
            $reward = $this->_modelFactory->create();
            $reward->setCustomerId($order->getCustomerId());
            $reward->setWebsiteId($websiteId);
            $reward->loadByCustomer();

            if (($order->getRewardPointsBalance() - $reward->getPointsBalance()) >= 0.0001) {
                $this->_session->setUpdateSection('payment-method');
                $this->_session->setGotoSection('payment');
                throw new \Magento\Reward\Model\Reward\Balance\Exception(
                    __('You don\'t have enough reward points to pay for this purchase.')
                );
            }
        }
    }
}
