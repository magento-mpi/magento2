<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model;

class PaymentDataImporter
{
    /**
     * Core model store configuration
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Reward factory
     *
     * @var \Magento\Reward\Model\RewardFactory
     */
    protected $_rewardFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @param RewardFactory $rewardFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        \Magento\Reward\Model\RewardFactory $rewardFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_rewardFactory = $rewardFactory;
        $this->customerFactory = $customerFactory;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * Prepare and set to quote reward balance instance,
     * set zero subtotal checkout payment if need
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @param \Magento\Framework\Object $payment
     * @param bool $useRewardPoints
     * @return $this
     */
    public function import($quote, $payment, $useRewardPoints)
    {
        if (!$quote ||
            !$quote->getCustomerId() ||
            $quote->getBaseGrandTotal() + $quote->getBaseRewardCurrencyAmount() <= 0
        ) {
            return $this;
        }
        $quote->setUseRewardPoints((bool)$useRewardPoints);
        if ($quote->getUseRewardPoints()) {
            $customerData = $quote->getCustomer();
            $customer = $this->customerFactory->create(
                [
                    'data' => $this->extensibleDataObjectConverter->toFlatArray($customerData),
                ]
            );
            /* @var $reward \Magento\Reward\Model\Reward */
            $reward = $this->_rewardFactory->create()->setCustomer($customer);
            $reward->setWebsiteId($quote->getStore()->getWebsiteId());
            $reward->loadByCustomer();
            $minPointsBalance = (int)$this->_scopeConfig->getValue(
                \Magento\Reward\Model\Reward::XML_PATH_MIN_POINTS_BALANCE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $quote->getStoreId()
            );

            if ($reward->getId() && $reward->getPointsBalance() >= $minPointsBalance) {
                $quote->setRewardInstance($reward);
                if (!$payment->getMethod()) {
                    $payment->setMethod('free');
                }
            } else {
                $quote->setUseRewardPoints(false);
            }
        }
        return $this;
    }
}
