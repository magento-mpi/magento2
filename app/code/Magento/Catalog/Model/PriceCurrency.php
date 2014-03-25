<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model;

/**
 * Class PriceCurrency model for convert and format price value
 */
class PriceCurrency implements \Magento\Pricing\PriceCurrencyInterface
{
    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var \Magento\Logger
     */
    protected $logger;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Logger $logger
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Logger $logger
    ) {
        $this->storeManager = $storeManager;
        $this->currencyFactory = $currencyFactory;
        $this->logger = $logger;
    }

    /**
     * Convert and format price value for specified store or passed currency
     *
     * @param float $amount
     * @param null|string|bool|int|\Magento\Core\Model\Store $store
     * @param \Magento\Directory\Model\Currency|string|null $currency
     * @return float
     */
    public function convert($amount, $store = null, $currency = null)
    {
        $currentCurrency = $this->getCurrency($store, $currency);
        return $this->getStore($store)->getBaseCurrency()->convert($amount, $currentCurrency);
    }

    /**
     * Format price value for specified store or passed currency
     *
     * @param float $amount
     * @param bool $includeContainer
     * @param int $precision
     * @param null|string|bool|int|\Magento\Core\Model\Store $store
     * @param \Magento\Directory\Model\Currency|string|null $currency
     * @return float
     */
    public function format(
        $amount,
        $includeContainer = true,
        $precision = self::DEFAULT_PRECISION,
        $store = null,
        $currency = null
    ) {
        return $this->getCurrency($store, $currency)->formatPrecision($amount, $precision, [], $includeContainer);
    }

    /**
     * Convert and format price value
     *
     * @param float $amount
     * @param bool $includeContainer
     * @param int $precision
     * @param null|string|bool|int|\Magento\Core\Model\Store $store
     * @param \Magento\Directory\Model\Currency|string|null $currency
     * @return string
     */
    public function convertAndFormat(
        $amount,
        $includeContainer = true,
        $precision = self::DEFAULT_PRECISION,
        $store = null,
        $currency = null
    ) {
        $amount = $this->convert($amount, $store, $currency);
        return $this->format($amount, $includeContainer, $precision, $store, $currency);
    }

    /**
     * Get currency model
     *
     * @param null|string|bool|int|\Magento\Core\Model\Store $store
     * @param \Magento\Directory\Model\Currency|string|null $currency
     * @return \Magento\Directory\Model\Currency
     */
    protected function getCurrency($store = null, $currency = null)
    {
        if ($currency instanceof \Magento\Directory\Model\Currency) {
            $currentCurrency = $currency;
        } elseif (is_string($currency)) {
            $currency = $this->currencyFactory->create()->load($currency);
            $baseCurrency = $this->getStore($store)->getBaseCurrency();
            $currentCurrency = $baseCurrency->getRate($currency) ? $currency : $baseCurrency;
        } else {
            $currentCurrency = $this->getStore($store)->getCurrentCurrency();
        }
        return $currentCurrency;
    }

    /**
     * Get store model
     *
     * @param null|string|bool|int|\Magento\Core\Model\Store $store
     * @return \Magento\Core\Model\Store
     */
    protected function getStore($store = null)
    {
        try {
            if (!$store instanceof \Magento\Core\Model\Store) {
                $store = $this->storeManager->getStore($store);
            }
        } catch (\Exception $e) {
            $this->logger->logException($e);
            $store = $this->storeManager->getStore();
        }
        return $store;
    }
}
