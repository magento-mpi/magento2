<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml dashboard bar block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Dashboard_Bar extends Magento_Adminhtml_Block_Dashboard_Abstract
{
    protected $_totals = array();
    protected $_currentCurrencyCode = null;

    protected function getTotals()
    {
        return $this->_totals;
    }

    public function addTotal($label, $value, $isQuantity=false)
    {
        /*if (!$isQuantity) {
            $value = $this->format($value);
            $decimals = substr($value, -2);
            $value = substr($value, 0, -2);
        } else {
            $value = ($value != '')?$value:0;
            $decimals = '';
        }*/
        if (!$isQuantity) {
            $value = $this->format($value);
        }
        $decimals = '';
        $this->_totals[] = array(
            'label' => $label,
            'value' => $value,
            'decimals' => $decimals,
        );

        return $this;
    }

    /**
     * Formating value specific for this store
     *
     * @param decimal $price
     * @return string
     */
    public function format($price)
    {
        return $this->getCurrency()->format($price);
    }

    /**
     * Setting currency model
     *
     * @param Magento_Directory_Model_Currency $currency
     */
    public function setCurrency($currency)
    {
        $this->_currency = $currency;
    }

    /**
     * Retrieve currency model if not set then return currency model for current store
     *
     * @return Magento_Directory_Model_Currency
     */
    public function getCurrency()
    {
        if (is_null($this->_currentCurrencyCode)) {
            if ($this->getRequest()->getParam('store')) {
                $this->_currentCurrencyCode = Mage::app()->getStore($this->getRequest()->getParam('store'))->getBaseCurrency();
            } else if ($this->getRequest()->getParam('website')){
                $this->_currentCurrencyCode = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getBaseCurrency();
            } else if ($this->getRequest()->getParam('group')){
                $this->_currentCurrencyCode =  Mage::app()->getGroup($this->getRequest()->getParam('group'))->getWebsite()->getBaseCurrency();
            } else {
                $this->_currentCurrencyCode = Mage::app()->getStore()->getBaseCurrency();
            }
        }

        return $this->_currentCurrencyCode;
    }
}
