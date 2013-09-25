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
 * Product price block
 *
 * @category   Magento
 * @package    Magento_Catalog
 */
class Magento_Catalog_Block_Product_Price extends Magento_Core_Block_Template
{
    protected $_priceDisplayType = null;
    protected $_idSuffix = '';

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * Tax data
     *
     * @var Magento_Tax_Helper_Data
     */
    protected $_taxData = null;

    /**
     * Catalog data
     *
     * @var Magento_Catalog_Helper_Data
     */
    protected $_catalogData = null;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Construct
     *
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Tax_Helper_Data $taxData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $registry;
        $this->_catalogData = $catalogData;
        $this->_taxData = $taxData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve product
     *
     * @return Magento_Catalog_Model_Product
     */
    public function getProduct()
    {
        $product = $this->_getData('product');
        if (!$product) {
            $product = $this->_coreRegistry->registry('product');
        }
        return $product;
    }

    public function getDisplayMinimalPrice()
    {
        return $this->_getData('display_minimal_price');
    }

    public function setIdSuffix($idSuffix)
    {
        $this->_idSuffix = $idSuffix;
        return $this;
    }

    public function getIdSuffix()
    {
        return $this->_idSuffix;
    }

    /**
     * Get tier prices (formatted)
     *
     * @param Magento_Catalog_Model_Product $product
     * @return array
     */
    public function getTierPrices($product = null)
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        $prices = $product->getFormatedTierPrice();

        $res = array();
        if (is_array($prices)) {
            foreach ($prices as $price) {
                $price['price_qty'] = $price['price_qty'] * 1;

                $productPrice = $product->getPrice();
                if ($product->getPrice() != $product->getFinalPrice()) {
                    $productPrice = $product->getFinalPrice();
                }

                // Group price must be used for percent calculation if it is lower
                $groupPrice = $product->getGroupPrice();
                if ($productPrice > $groupPrice) {
                    $productPrice = $groupPrice;
                }

                if ($price['price'] < $productPrice) {
                    $price['savePercent'] = ceil(100 - ((100 / $productPrice) * $price['price']));

                    $tierPrice = $this->_storeManager->getStore()->convertPrice(
                        $this->_taxData->getPrice($product, $price['website_price'])
                    );
                    $price['formated_price'] = $this->_storeManager->getStore()->formatPrice($tierPrice);
                    $price['formated_price_incl_tax'] = $this->_storeManager->getStore()->formatPrice(
                        $this->_storeManager->getStore()->convertPrice(
                            $this->_taxData->getPrice($product, $price['website_price'], true)
                        )
                    );

                    if ($this->_catalogData->canApplyMsrp($product)) {
                        $oldPrice = $product->getFinalPrice();
                        $product->setPriceCalculation(false);
                        $product->setPrice($tierPrice);
                        $product->setFinalPrice($tierPrice);

                        $this->getLayout()->getBlock('product.info')->getPriceHtml($product);
                        $product->setPriceCalculation(true);

                        $price['real_price_html'] = $product->getRealPriceHtml();
                        $product->setFinalPrice($oldPrice);
                    }

                    $res[] = $price;
                }
            }
        }

        return $res;
    }

    /**
     * Retrieve url for direct adding product to cart
     *
     * @param Magento_Catalog_Model_Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product, $additional = array())
    {
        return $this->helper('Magento_Checkout_Helper_Cart')->getAddUrl($product, $additional);
    }

    /**
     * Prevent displaying if the price is not available
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->getProduct() || $this->getProduct()->getCanShowPrice() === false) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Get Product Price valid JS string
     *
     * @param Magento_Catalog_Model_Product $product
     * @return string
     */
    public function getRealPriceJs($product)
    {
        $html = $this->hasRealPriceHtml() ? $this->getRealPriceHtml() : $product->getRealPriceHtml();
        return $this->_coreData->jsonEncode($html);
    }
}
