<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CurrencySymbol
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Currency Symbol Observer
 *
 * @category    Magento
 * @package     Magento_CurrencySymbol
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CurrencySymbol\Model;

class Observer
{
    /**
     * Currency symbol data
     *
     * @var Magento_CurrencySymbol_Helper_Data
     */
    protected $_currencySymbolData = null;

    /**
     * @param Magento_CurrencySymbol_Helper_Data $currencySymbolData
     */
    public function __construct(
        Magento_CurrencySymbol_Helper_Data $currencySymbolData
    ) {
        $this->_currencySymbolData = $currencySymbolData;
    }

    /**
     * Generate options for currency displaying with custom currency symbol
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_CurrencySymbol_Model__Observer
     */
    public function currencyDisplayOptions(\Magento\Event\Observer $observer)
    {
        $baseCode = $observer->getEvent()->getBaseCode();
        $currencyOptions = $observer->getEvent()->getCurrencyOptions();
        $currencyOptions->setData($this->_currencySymbolData->getCurrencyOptions($baseCode));

        return $this;
    }
}
