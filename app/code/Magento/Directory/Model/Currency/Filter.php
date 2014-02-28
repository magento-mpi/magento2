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
     * @var \Zend_Currency
     */
    protected $_currency;

    /**
     * @var \Magento\LocaleInterface
     */
    protected $_locale;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\LocaleInterface $locale
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param string $code
     * @param int $rate
     */
    public function __construct(
        \Magento\LocaleInterface $locale,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        $code,
        $rate = 1
    ) {
        $this->_locale = $locale;
        $this->_storeManager = $storeManager;
        $this->_currency = $this->_locale->currency($code);
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
        $value = $this->_locale->getNumber($value);
        $value = $this->_storeManager->getStore()->roundPrice($this->_rate*$value);
        $value = sprintf("%f", $value);
        return $this->_currency->toCurrency($value);
    }
}
