<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product options abstract type block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Catalog_Block_Product_View_Options_Abstract extends Magento_Core_Block_Template
{
    /**
     * Product object
     *
     * @var Magento_Catalog_Model_Product
     */
    protected $_product;

    /**
     * Product option object
     *
     * @var Magento_Catalog_Model_Product_Option
     */
    protected $_option;

    /**
     * Tax data
     *
     * @var Magento_Tax_Helper_Data
     */
    protected $_taxData = null;

    /**
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Tax_Helper_Data $taxData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_taxData = $taxData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Set Product object
     *
     * @param Magento_Catalog_Model_Product $product
     * @return Magento_Catalog_Block_Product_View_Options_Abstract
     */
    public function setProduct(Magento_Catalog_Model_Product $product = null)
    {
        $this->_product = $product;
        return $this;
    }

    /**
     * Retrieve Product object
     *
     * @return Magento_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->_product;
    }

    /**
     * Set option
     *
     * @param Magento_Catalog_Model_Product_Option $option
     * @return Magento_Catalog_Block_Product_View_Options_Abstract
     */
    public function setOption(Magento_Catalog_Model_Product_Option $option)
    {
        $this->_option = $option;
        return $this;
    }

    /**
     * Get option
     *
     * @return Magento_Catalog_Model_Product_Option
     */
    public function getOption()
    {
        return $this->_option;
    }

    public function getFormatedPrice()
    {
        if ($option = $this->getOption()) {
            return $this->_formatPrice(array(
                'is_percent'    => ($option->getPriceType() == 'percent'),
                'pricing_value' => $option->getPrice($option->getPriceType() == 'percent')
            ));
        }
        return '';
    }

    /**
     * Return formated price
     *
     * @param array $value
     * @return string
     */
    protected function _formatPrice($value, $flag=true)
    {
        if ($value['pricing_value'] == 0) {
            return '';
        }

        $store = $this->getProduct()->getStore();

        $sign = '+';
        if ($value['pricing_value'] < 0) {
            $sign = '-';
            $value['pricing_value'] = 0 - $value['pricing_value'];
        }

        $priceStr = $sign;
        $_priceInclTax = $this->getPrice($value['pricing_value'], true);
        $_priceExclTax = $this->getPrice($value['pricing_value']);
        if ($this->_taxData->displayPriceIncludingTax()) {
            $priceStr .= $this->helper('Magento_Core_Helper_Data')->currencyByStore($_priceInclTax, $store, true, $flag);
        } elseif ($this->_taxData->displayPriceExcludingTax()) {
            $priceStr .= $this->helper('Magento_Core_Helper_Data')->currencyByStore($_priceExclTax, $store, true, $flag);
        } elseif ($this->_taxData->displayBothPrices()) {
            $priceStr .= $this->helper('Magento_Core_Helper_Data')->currencyByStore($_priceExclTax, $store, true, $flag);
            if ($_priceInclTax != $_priceExclTax) {
                $priceStr .= ' ('.$sign.$this->helper('Magento_Core_Helper_Data')
                    ->currencyByStore($_priceInclTax, $store, true, $flag).' '.__('Incl. Tax').')';
            }
        }

        if ($flag) {
            $priceStr = '<span class="price-notice">'.$priceStr.'</span>';
        }

        return $priceStr;
    }

    /**
     * Get price with including/excluding tax
     *
     * @param decimal $price
     * @param bool $includingTax
     * @return decimal
     */
    public function getPrice($price, $includingTax = null)
    {
        if (!is_null($includingTax)) {
            $price = $this->_taxData->getPrice($this->getProduct(), $price, true);
        } else {
            $price = $this->_taxData->getPrice($this->getProduct(), $price);
        }
        return $price;
    }

    /**
     * Returns price converted to current currency rate
     *
     * @param float $price
     * @return float
     */
    public function getCurrencyPrice($price)
    {
        $store = $this->getProduct()->getStore();
        return $this->helper('Magento_Core_Helper_Data')->currencyByStore($price, $store, false);
    }
}
