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
    protected $_rewardHelper;

    /**
     * Customer balance data
     *
     * @var \Magento\CustomerBalance\Helper\Data
     */
    protected $_customerBalanceHelper;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Reward\Helper\Data $rewardHelper
     * @param \Magento\CustomerBalance\Helper\Data $customerBalanceHelper
     * @param bool $dbCompatibleMode
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Reward\Helper\Data $rewardHelper,
        \Magento\CustomerBalance\Helper\Data $customerBalanceHelper,
        $dbCompatibleMode = true
    ) {
        $this->_rewardHelper = $rewardHelper;
        $this->_customerBalanceHelper = $customerBalanceHelper;
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
            return $this->_rewardHelper->isEnabled();
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
            return $this->_customerBalanceHelper->isEnabled();
        }
        return false;
    }
}
