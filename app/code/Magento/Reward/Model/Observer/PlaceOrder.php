<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

use Magento\Framework\Event\Observer;

class PlaceOrder
{
    /**
     * Reward place order restriction interface
     *
     * @var \Magento\Reward\Model\Observer\PlaceOrder\RestrictionInterface
     */
    protected $_restriction;

    /**
     * Reward model factory
     *
     * @var \Magento\Reward\Model\RewardFactory
     */
    protected $_modelFactory;

    /**
     * Reward resource model factory
     *
     * @var \Magento\Reward\Model\Resource\RewardFactory
     */
    protected $_resourceFactory;

    /**
     * Core model store manager interface
     *
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Reward balance validator
     *
     * @var \Magento\Reward\Model\Reward\Balance\Validator
     */
    protected $_validator;

    /**
     * @param \Magento\Reward\Model\Observer\PlaceOrder\RestrictionInterface $restriction
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Reward\Model\RewardFactory $modelFactory
     * @param \Magento\Reward\Model\Resource\RewardFactory $resourceFactory
     * @param \Magento\Reward\Model\Reward\Balance\Validator $validator
     */
    public function __construct(
        \Magento\Reward\Model\Observer\PlaceOrder\RestrictionInterface $restriction,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Reward\Model\RewardFactory $modelFactory,
        \Magento\Reward\Model\Resource\RewardFactory $resourceFactory,
        \Magento\Reward\Model\Reward\Balance\Validator $validator
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
     * @param Observer $observer
     * @return void
     */
    public function dispatch(Observer $observer)
    {
        if (false == $this->_restriction->isAllowed()) {
            return;
        }

        /* @var $order \Magento\Sales\Model\Order */
        $order = $observer->getEvent()->getOrder();

        if ($order->getBaseRewardCurrencyAmount() > 0) {
            $this->_validator->validate($order);

            /** @var $model \Magento\Reward\Model\Reward */
            $model = $this->_modelFactory->create();
            $model->setCustomerId($order->getCustomerId());
            $model->setWebsiteId($this->_storeManager->getStore($order->getStoreId())->getWebsiteId());
            $model->setPointsDelta(-$order->getRewardPointsBalance());
            $model->setAction(\Magento\Reward\Model\Reward::REWARD_ACTION_ORDER);
            $model->setActionEntity($order);
            $model->updateRewardPoints();
        }
        $ruleIds = explode(',', $order->getAppliedRuleIds());
        $ruleIds = array_unique($ruleIds);
        /** @var $resource \Magento\Reward\Model\Resource\Reward */
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
