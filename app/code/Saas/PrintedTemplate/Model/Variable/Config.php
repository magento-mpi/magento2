<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Container for display configuration data
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Variable_Config extends Saas_PrintedTemplate_Model_Variable_Abstract
{
    /**
     * Tax configuration model
     *
     * @var Magento_Tax_Model_Config
     */
    protected $_taxConfig;

    /**
     * Initialize model
     *
     * @see Saas_PrintedTemplate_Model_Variable_Abstract::_initVariable()
     */
    protected function _initVariable()
    {
        $this->_taxConfig = Mage::helper('Magento_Tax_Helper_Data')->getConfig();
    }

    /**
     * Proxy to tax config
     * @see Magento_Tax_Model_Config::displaySalesSubtotalInclTax()
     */
    public function getDisplaySubtotalInclTax()
    {
        return $this->_taxConfig->displaySalesSubtotalInclTax();
    }

    /**
     * Proxy to tax config
     * @see Magento_Tax_Model_Config::displaySalesSubtotalExclTax()
     */
    public function getDisplaySubtotalExclTax()
    {
        return $this->_taxConfig->displaySalesSubtotalExclTax();
    }

    /**
     * Proxy to tax config
     * @see Magento_Tax_Model_Config::displaySalesSubtotalBoth()
     */
    public function getDisplaySubtotalBoth()
    {
        return $this->_taxConfig->displaySalesSubtotalBoth();
    }

    /**
     * Proxy to tax config
     * @see Magento_Tax_Model_Config::displaySalesShippingInclTax()
     */
    public function getDisplayShippingInclTax()
    {
        return $this->_taxConfig->displaySalesShippingInclTax();
    }

    /**
     * Proxy to tax config
     * @see Magento_Tax_Model_Config::displaySalesShippingExclTax()
     */
    public function getDisplayShippingExclTax()
    {
        return $this->_taxConfig->displaySalesShippingExclTax();
    }

    /**
     * Proxy to tax config
     * @see Magento_Tax_Model_Config::displaySalesShippingBoth()
     */
    public function getDisplayShippingBoth()
    {
        return $this->_taxConfig->displaySalesShippingBoth();
    }

    /**
     * Proxy to tax config
     * @see Magento_Tax_Model_Config::displaySalesDiscountInclTax()
     */
    public function getDisplaySalesDiscountInclTax()
    {
        return $this->_taxConfig->displaySalesDiscountInclTax();
    }

    /**
     * Proxy to tax config
     * @see Magento_Tax_Model_Config::displaySalestDiscountExclTax()
     */
    public function getDisplayDiscountExclTax()
    {
        return $this->_taxConfig->displaySalestDiscountExclTax();
    }

    /**
     * Proxy to tax config
     * @see Magento_Tax_Model_Config::displaySalesDiscountBoth()
     */
    public function getDisplayDiscountBoth()
    {
        return $this->_taxConfig->displaySalesDiscountBoth();
    }

    /**
     * Proxy to tax config
     * @see Magento_Tax_Model_Config::displaySalesTaxWithGrandTotal()
     */
    public function getDisplayTaxWithGrandTotal()
    {
        return $this->_taxConfig->displaySalesTaxWithGrandTotal();
    }

    /**
     * Proxy to tax config
     * @see Magento_Tax_Model_Config::displaySalesZeroTax()
     */
    public function getDisplayZeroTax()
    {
        return $this->_taxConfig->displaySalesZeroTax();
    }

    /**
     * Proxy to logo from Conf -> Sales -> Invoice and Packing Slip Design
     * @see Saas_Page_Block_Html::getPrintLogoUrl()
     */
    public function getStoreLogoUrl()
    {
        return Mage::getBlockSingleton('Magento_Page_Block_Html')->getPrintLogoUrl();
    }

    /**
     * Proxy address from Conf -> Sales -> Invoice and Packing Slip Design
     * @see Saas_Page_Block_Html::getPrintLogoText()
     */
    public function getStoreAddress()
    {
        return nl2br(Mage::getBlockSingleton('Magento_Page_Block_Html')->getPrintLogoText());
    }
}
