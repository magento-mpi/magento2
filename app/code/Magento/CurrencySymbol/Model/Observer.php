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
     * Generate options for currency displaying with custom currency symbol
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_CurrencySymbol_Model__Observer
     */
    public function currencyDisplayOptions(\Magento\Event\Observer $observer)
    {
        $baseCode = $observer->getEvent()->getBaseCode();
        $currencyOptions = $observer->getEvent()->getCurrencyOptions();
        $currencyOptions->setData(\Mage::helper('Magento\CurrencySymbol\Helper\Data')->getCurrencyOptions($baseCode));

        return $this;
    }
}
