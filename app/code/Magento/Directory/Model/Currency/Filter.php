<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Currency filter
 */
namespace Magento\Directory\Model\Currency;

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
     * @var \Magento\CurrencyInterface
     */
    protected $_currency;

    /**
     * @var \Magento\Locale\FormatInterface
     */
    protected $_localeFormat;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Locale\CurrencyInterface
     */
    protected $_localeCurrency;

    /**
     * @param \Magento\Locale\FormatInterface $localeFormat
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Locale\CurrencyInterface $localeCurrency
     * @param string $code
     * @param int $rate
     */
    public function __construct(
        \Magento\Locale\FormatInterface $localeFormat,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Locale\CurrencyInterface $localeCurrency,
        $code,
        $rate = 1
    ) {
        $this->_localeFormat = $localeFormat;
        $this->_storeManager = $storeManager;
        $this->_currency = $localeCurrency->getCurrency($code);
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
        $value = $this->_storeManager->getStore()->roundPrice($this->_rate*$value);
        $value = sprintf("%f", $value);
        return $this->_currency->toCurrency($value);
    }
}
