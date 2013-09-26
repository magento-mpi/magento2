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
class Magento_Directory_Model_Currency_Filter implements Zend_Filter_Interface
{
    /**
     * Rate value
     *
     * @var decimal
     */
    protected $_rate;

    /**
     * Currency object
     *
     * @var Zend_Currency
     */
    protected $_currency;

    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param string $code
     * @param int $rate
     */
    public function __construct(
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Core_Model_StoreManagerInterface $storeManager,
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
     * @param double $rate
     */
    public function setRate($rate)
    {
        $this->_rate = $rate;
    }

    /**
     * Filter value
     *
     * @param   double $value
     * @return  string
     */
    public function filter($value)
    {
        $value = $this->_locale->getNumber($value);
        $value = $this->_storeManager->getStore()->roundPrice($this->_rate*$value);
        $value = sprintf("%f", $value);
        return $this->_currency->toCurrency($value);
    }
}
