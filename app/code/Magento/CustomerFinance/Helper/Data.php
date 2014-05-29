<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerFinance\Helper;

/**
 * CustomerFinance Data Helper
 */
class Data extends \Magento\Core\Helper\Data
{
    /**
     * Reward data
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardData = null;

    /**
     * Customer balance data
     *
     * @var \Magento\CustomerBalance\Helper\Data
     */
    protected $_customerBalanceData = null;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Reward\Helper\Data $rewardData
     * @param \Magento\CustomerBalance\Helper\Data $customerBalanceData
     * @param bool $dbCompatibleMode
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Reward\Helper\Data $rewardData,
        \Magento\CustomerBalance\Helper\Data $customerBalanceData,
        $dbCompatibleMode = true
    ) {
        $this->_rewardData = $rewardData;
        $this->_customerBalanceData = $customerBalanceData;
        parent::__construct(
            $context,
            $scopeConfig,
            $storeManager,
            $appState,
            $priceCurrency,
            $dbCompatibleMode
        );
    }

    /**
     * Is reward points enabled
     *
     * @return bool
     */
    public function isRewardPointsEnabled()
    {
        if ($this->isModuleEnabled('Magento_Reward')) {
            return $this->_rewardData->isEnabled();
        }
        return false;
    }

    /**
     * Is store credit enabled
     *
     * @return bool
     */
    public function isCustomerBalanceEnabled()
    {
        if ($this->isModuleEnabled('Magento_CustomerBalance')) {
            return $this->_customerBalanceData->isEnabled();
        }
        return false;
    }
}
