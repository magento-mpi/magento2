<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Currency filter
 */
namespace Magento\Directory\Model\Currency;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Filter implements \Zend_Filter_Interface
{
    /**
     * Rate value
     *
     * @var float
     */
    protected $_rate;

    /**
     * Currency object
     *
     * @var \Magento\Framework\CurrencyInterface
     */
    protected $_currency;

    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    protected $_localeFormat;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_localeCurrency;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param PriceCurrencyInterface $priceCurrency
     * @param $code
     * @param int $rate
     */
    public function __construct(
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        PriceCurrencyInterface $priceCurrency,
        $code,
        $rate = 1
    ) {
        $this->_localeFormat = $localeFormat;
        $this->_storeManager = $storeManager;
        $this->_currency = $localeCurrency->getCurrency($code);
        $this->priceCurrency = $priceCurrency;
        $this->_rate = $rate;
    }

    /**
     * Set filter rate
     *
     * @param float $rate
     * @return void
     */
    public function setRate($rate)
    {
        $this->_rate = $rate;
    }

    /**
     * Filter value
     *
     * @param float $value
     * @return string
     */
    public function filter($value)
    {
        $value = $this->_localeFormat->getNumber($value);
        $value = $this->priceCurrency->round($this->_rate * $value);
        $value = sprintf("%f", $value);
        return $this->_currency->toCurrency($value);
    }
}
