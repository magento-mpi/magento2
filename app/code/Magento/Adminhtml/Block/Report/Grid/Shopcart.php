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
 * Adminhtml shopping carts report grid block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Report\Grid;

class Shopcart extends \Magento\Adminhtml\Block\Widget\Grid
{
    /**
     * stores current currency code
     */
    protected $_currentCurrencyCode = null;

    /**
     * ids of current stores
     */
    protected $_storeIds            = array();

    /**
     * storeIds setter
     *
     * @param  array $storeIds
     * @return Magento_Adminhtml_Block_Report_Grid_Shopcart_Abstract
     */
    public function setStoreIds($storeIds)
    {
        $this->_storeIds = $storeIds;
        return $this;
    }

    /**
     * Retrieve currency code based on selected store
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        if (is_null($this->_currentCurrencyCode)) {
            reset($this->_storeIds);
            $this->_currentCurrencyCode = (count($this->_storeIds) > 0)
                ? \Mage::app()->getStore(current($this->_storeIds))->getBaseCurrencyCode()
                : \Mage::app()->getStore()->getBaseCurrencyCode();
        }
        return $this->_currentCurrencyCode;
    }

    /**
     * Get currency rate (base to given currency)
     *
     * @param string|\Magento\Directory\Model\Currency $currencyCode
     * @return double
     */
    public function getRate($toCurrency)
    {
        return \Mage::app()->getStore()->getBaseCurrency()->getRate($toCurrency);
    }
}
