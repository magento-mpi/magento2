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
 *
 * @author      Magento Core Team <core@magentocommerce.com>
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

    public function __construct($code, $rate=1)
    {
        $this->_currency = Mage::app()->getLocale()->currency($code);
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
        $value = Mage::app()->getLocale()->getNumber($value);
        $value = Mage::app()->getStore()->roundPrice($this->_rate*$value);
        //$value = round($value, 2);
        $value = sprintf("%f", $value);
        return $this->_currency->toCurrency($value);
    }
}
