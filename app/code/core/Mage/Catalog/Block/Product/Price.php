<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product price block
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Block_Product_Price extends Mage_Core_Block_Template
{
    protected $_priceDisplayType = null;
    protected $_idSuffix = '';

    public function getProduct()
    {
        $product = $this->_getData('product');
        if (!$product) {
            $product = Mage::registry('product');
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
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getTierPrices($product = null)
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        $prices  = $product->getFormatedTierPrice();

        $res = array();
        if (is_array($prices)) {
            foreach ($prices as $price) {
                $price['price_qty'] = $price['price_qty']*1;

                if ($product->getPrice() != $product->getFinalPrice()) {
                    $productPrice = $product->getFinalPrice();
                } else {
                    $productPrice = $product->getPrice();
                }

                if ($price['price']<$productPrice) {
                    $price['savePercent'] = ceil(100 - (( 100/$productPrice ) * $price['price'] ));

                    $tierPrice = Mage::app()->getStore()->convertPrice(
                        Mage::helper('Mage_Tax_Helper_Data')->getPrice($product, $price['website_price'])
                    );
                    $price['formated_price'] = Mage::app()->getStore()->formatPrice($tierPrice);
                    $price['formated_price_incl_tax'] = Mage::app()->getStore()->formatPrice(
                        Mage::app()->getStore()->convertPrice(
                            Mage::helper('Mage_Tax_Helper_Data')->getPrice($product, $price['website_price'], true)
                        )
                    );

                    if (Mage::helper('Mage_Catalog_Helper_Data')->canApplyMsrp($product)) {
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
     * @param Mage_Catalog_Model_Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product, $additional = array())
    {
        return $this->helper('Mage_Checkout_Helper_Cart')->getAddUrl($product, $additional);
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
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getRealPriceJs($product)
    {
        $html = $this->hasRealPriceHtml() ? $this->getRealPriceHtml() : $product->getRealPriceHtml();
        return Mage::helper('Mage_Core_Helper_Data')->jsonEncode($html);
    }
}
