<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Bundle product price block
 *
 * @category   Magento
 * @package    Magento_Bundle
 */
class Magento_Bundle_Block_Catalog_Product_Price extends Magento_Catalog_Block_Product_Price
{
    /**
     * @var Magento_Tax_Model_Calculation
     */
    protected $_taxCalc;

    /**
     * @param Magento_Tax_Model_Calculation $taxCalc
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param array $data
     */
    public function __construct(
        Magento_Tax_Model_Calculation $taxCalc,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Tax_Helper_Data $taxData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_StoreManager $storeManager,
        array $data = array()
    ) {
        parent::__construct($storeManager, $catalogData, $taxData, $coreData, $context, $registry, $data);
        $this->_taxCalc = $taxCalc;
    }

    public function isRatesGraterThenZero()
    {
        $_request = $this->_taxCalc->getRateRequest(false, false, false);
        $_request->setProductClassId($this->getProduct()->getTaxClassId());
        $defaultTax = $this->_taxCalc->getRate($_request);

        $_request = $this->_taxCalc->getRateRequest();
        $_request->setProductClassId($this->getProduct()->getTaxClassId());
        $currentTax = $this->_taxCalc->getRate($_request);

        return (floatval($defaultTax) > 0 || floatval($currentTax) > 0);
    }

    /**
     * Check if we have display prices including and excluding tax
     * With corrections for Dynamic prices
     *
     * @return bool
     */
    public function displayBothPrices()
    {
        $product = $this->getProduct();
        if ($product->getPriceType() == Magento_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC &&
            $product->getPriceModel()->getIsPricesCalculatedByIndex() !== false) {
            return false;
        }
        return $this->helper('Magento_Tax_Helper_Data')->displayBothPrices();
    }

    /**
     * Convert block to html string
     *
     * @return string
     */
    protected function _toHtml()
    {
        $product = $this->getProduct();
        if ($this->getMAPTemplate() && $this->_catalogData->canApplyMsrp($product)
                && $product->getPriceType() != Magento_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC
        ) {
            $hiddenPriceHtml = parent::_toHtml();
            if ($this->_catalogData->isShowPriceOnGesture($product)) {
                $this->setWithoutPrice(true);
            }
            $realPriceHtml = parent::_toHtml();
            $this->unsWithoutPrice();
            $addToCartUrl  = $this->getLayout()->getBlock('product.info.bundle')->getAddToCartUrl($product);
            $product->setAddToCartUrl($addToCartUrl);
            $html = $this->getLayout()
                ->createBlock('Magento_Catalog_Block_Product_Price')
                ->setTemplate($this->getMAPTemplate())
                ->setRealPriceHtml($hiddenPriceHtml)
                ->setPriceElementIdPrefix('bundle-price-')
                ->setIdSuffix($this->getIdSuffix())
                ->setProduct($product)
                ->toHtml();

            return $realPriceHtml . $html;
        }

        return parent::_toHtml();
    }

    /**
     * @param null|string|bool|int|Magento_Core_Model_Store $storeId
     * @return bool|Magento_Core_Model_Website
     */
    public function getWebsite($storeId)
    {
        return $this->_storeManager->getStore($storeId)->getWebsite();
    }
}
