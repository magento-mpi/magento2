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
 * Reward Points Payment block in admin order creating process
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reward_Block_Adminhtml_Sales_Order_Create_Payment extends Magento_Backend_Block_Template
{
    /**
     * Reward data
     *
     * @var Magento_Reward_Helper_Data
     */
    protected $_rewardData = null;

    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @var Magento_Adminhtml_Model_Sales_Order_Create
     */
    protected $_orderCreate;

    /**
     * @var Magento_Reward_Model_RewardFactory
     */
    protected $_rewardFactory;

    /**
     * @param Magento_Reward_Helper_Data $rewardData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Adminhtml_Model_Sales_Order_Create $orderCreate
     * @param Magento_Reward_Model_RewardFactory $rewardFactory
     * @param array $data
     */
    public function __construct(
        Magento_Reward_Helper_Data $rewardData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Adminhtml_Model_Sales_Order_Create $orderCreate,
        Magento_Reward_Model_RewardFactory $rewardFactory,
        array $data = array()
    ) {
        $this->_rewardData = $rewardData;
        $this->_storeManager = $storeManager;
        $this->_orderCreate = $orderCreate;
        $this->_rewardFactory = $rewardFactory;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Getter
     *
     * @return Magento_Adminhtml_Model_Sales_Order_Create
     */
    protected function _getOrderCreateModel()
    {
        return $this->_orderCreate;
    }

    /**
     * Getter
     *
     * @return Magento_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_getOrderCreateModel()->getQuote();
    }

    /**
     * Check whether can use customer reward points
     *
     * @return boolean
     */
    public function canUseRewardPoints()
    {
        $websiteId = $this->_storeManager->getStore($this->getQuote()->getStoreId())->getWebsiteId();
        $minPointsBalance = (int)$this->_storeConfig->getConfig(
            Magento_Reward_Model_Reward::XML_PATH_MIN_POINTS_BALANCE,
            $this->getQuote()->getStoreId()
        );

        return $this->getReward()->getPointsBalance() >= $minPointsBalance
            && $this->_rewardData->isEnabledOnFront($websiteId)
            && $this->_authorization->isAllowed(Magento_Reward_Helper_Data::XML_PATH_PERMISSION_AFFECT)
            && (float)$this->getCurrencyAmount()
            && $this->getQuote()->getBaseGrandTotal() + $this->getQuote()->getBaseRewardCurrencyAmount() > 0;
    }

    /**
     * Getter.
     * Retrieve reward points model
     *
     * @return Magento_Reward_Model_Reward
     */
    public function getReward()
    {
        if (!$this->_getData('reward')) {
            /* @var $reward Magento_Reward_Model_Reward */
            $reward = $this->_rewardFactory->create()
                ->setCustomer($this->getQuote()->getCustomer())
                ->setStore($this->getQuote()->getStore())
                ->loadByCustomer();
            $this->setData('reward', $reward);
        }
        return $this->_getData('reward');
    }

    /**
     * Prepare some template data
     *
     * @return string
     */
    protected function _toHtml()
    {
        $points = $this->getReward()->getPointsBalance();
        $amount = $this->getReward()->getCurrencyAmount();
        $rewardFormatted = $this->_rewardData
            ->formatReward($points, $amount, $this->getQuote()->getStore()->getId());
        $this->setPointsBalance($points)->setCurrencyAmount($amount)
            ->setUseLabel(__('Use my reward points; %1 are available.', $rewardFormatted))
        ;
        return parent::_toHtml();
    }

    /**
     * Check if reward points applied in quote
     *
     * @return boolean
     */
    public function useRewardPoints()
    {
        return (bool)$this->_getOrderCreateModel()->getQuote()->getUseRewardPoints();
    }
}
