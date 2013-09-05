<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Reward_Model_Observer_PlaceOrder
{
    /**
     * @var Magento_Reward_Model_Observer_PlaceOrder_RestrictionInterface
     */
    protected $_restriction;

    /**
     * @var Magento_Reward_Model_RewardFactory
     */
    protected $_modelFactory;

    /**
     * @var Magento_Reward_Model_Resource_RewardFactory
     */
    protected $_resourceFactory;

    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @var Magento_Reward_Model_Reward_Balance_Validator
     */
    protected $_validator;

    /**
     * @param Magento_Reward_Model_Observer_PlaceOrder_RestrictionInterface $restriction
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Reward_Model_RewardFactory $modelFactory
     * @param Magento_Reward_Model_Resource_RewardFactory $resourceFactory
     * @param Magento_Reward_Model_Reward_Balance_Validator $validator
     */
    public function __construct(
        Magento_Reward_Model_Observer_PlaceOrder_RestrictionInterface $restriction,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Reward_Model_RewardFactory $modelFactory,
        Magento_Reward_Model_Resource_RewardFactory $resourceFactory,
        Magento_Reward_Model_Reward_Balance_Validator $validator
    ) {
        $this->_restriction = $restriction;
        $this->_storeManager = $storeManager;
        $this->_modelFactory = $modelFactory;
        $this->_resourceFactory = $resourceFactory;
        $this->_validator = $validator;
    }

    /**
     * Reduce reward points if points was used during checkout
     *
     * @param \Magento\Event\Observer $observer
     */
    public function dispatch(\Magento\Event\Observer $observer)
    {
        if (false == $this->_restriction->isAllowed()) {
            return;
        }

        /* @var $order Magento_Sales_Model_Order */
        $order = $observer->getEvent()->getOrder();

        if ($order->getBaseRewardCurrencyAmount() > 0) {
            $this->_validator->validate($order);

            /** @var $model Magento_Reward_Model_Reward */
            $model = $this->_modelFactory->create();
            $model->setCustomerId($order->getCustomerId());
            $model->setWebsiteId($this->_storeManager->getStore($order->getStoreId())->getWebsiteId());
            $model->setPointsDelta(-$order->getRewardPointsBalance());
            $model->setAction(Magento_Reward_Model_Reward::REWARD_ACTION_ORDER);
            $model->setActionEntity($order);
            $model->updateRewardPoints();
        }
        $ruleIds = explode(',', $order->getAppliedRuleIds());
        $ruleIds = array_unique($ruleIds);
        /** @var $resource Magento_Reward_Model_Resource_Reward */
        $resource = $this->_resourceFactory->create();
        $data = $resource->getRewardSalesrule($ruleIds);
        $pointsDelta = 0;
        foreach ($data as $rule) {
            $pointsDelta += (int)$rule['points_delta'];
        }

        if ($pointsDelta) {
            $order->setRewardSalesrulePoints($pointsDelta);
        }
    }
}
