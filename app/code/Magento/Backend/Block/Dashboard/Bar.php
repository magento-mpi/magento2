<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\Dashboard;

/**
 * Adminhtml dashboard bar block
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Bar extends \Magento\Backend\Block\Dashboard\AbstractDashboard
{
    /**
     * @var array
     */
    protected $_totals = array();

    /**
     * @var \Magento\Directory\Model\Currency|null
     */
    protected $_currentCurrencyCode = null;

    /**
     * @return array
     */
    public function getTotals()
    {
        return $this->_totals;
    }

    /**
     * @param string $label
     * @param float $value
     * @param bool $isQuantity
     * @return $this
     */
    public function addTotal($label, $value, $isQuantity = false)
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
        $this->_totals[] = array('label' => $label, 'value' => $value, 'decimals' => $decimals);

        return $this;
    }

    /**
     * Formatting value specific for this store
     *
     * @param float $price
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
     * @return void
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
                $this->_currentCurrencyCode = $this->_storeManager->getStore(
                    $this->getRequest()->getParam('store')
                )->getBaseCurrency();
            } elseif ($this->getRequest()->getParam('website')) {
                $this->_currentCurrencyCode = $this->_storeManager->getWebsite(
                    $this->getRequest()->getParam('website')
                )->getBaseCurrency();
            } elseif ($this->getRequest()->getParam('group')) {
                $this->_currentCurrencyCode = $this->_storeManager->getGroup(
                    $this->getRequest()->getParam('group')
                )->getWebsite()->getBaseCurrency();
            } else {
                $this->_currentCurrencyCode = $this->_storeManager->getStore()->getBaseCurrency();
            }
        }

        return $this->_currentCurrencyCode;
    }
}
