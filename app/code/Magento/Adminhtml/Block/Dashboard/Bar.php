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

namespace Magento\Adminhtml\Block\Dashboard;

class Bar extends \Magento\Adminhtml\Block\Dashboard\AbstractDashboard
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
     * @param \Magento\Directory\Model\Currency $currency
     */
    public function setCurrency($currency)
    {
        $this->_currency = $currency;
    }

    /**
     * Retrieve currency model if not set then return currency model for current store
     *
     * @return \Magento\Directory\Model\Currency
     */
    public function getCurrency()
    {
        if (is_null($this->_currentCurrencyCode)) {
            if ($this->getRequest()->getParam('store')) {
                $this->_currentCurrencyCode = $this->_storeManager->getStore($this->getRequest()->getParam('store'))
                    ->getBaseCurrency();
            } elseif ($this->getRequest()->getParam('website')) {
                $this->_currentCurrencyCode = $this->_storeManager->getWebsite($this->getRequest()->getParam('website'))
                    ->getBaseCurrency();
            } elseif ($this->getRequest()->getParam('group')) {
                $this->_currentCurrencyCode =  $this->_storeManager->getGroup($this->getRequest()->getParam('group'))
                    ->getWebsite()
                    ->getBaseCurrency();
            } else {
                $this->_currentCurrencyCode = $this->_storeManager->getStore()->getBaseCurrency();
            }
        }

        return $this->_currentCurrencyCode;
    }
}
