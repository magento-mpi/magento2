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
 * Adminhtml block for fieldset of grouped product
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Composite_Fieldset_Grouped extends Magento_Catalog_Block_Product_View_Type_Grouped
{
    public function __construct(Magento_Catalog_Helper_Image $catalogImage, Magento_Page_Helper_Layout $pageLayout, Magento_Catalog_Helper_Product_Compare $catalogProductCompare, Magento_Wishlist_Helper_Data $wishlistData, Magento_Checkout_Helper_Cart $checkoutCart, Magento_Tax_Helper_Data $taxData, Magento_Catalog_Helper_Data $catalogData, Magento_Core_Helper_Data $coreData, Magento_Core_Block_Template_Context $context, array $data = array())
    {
        parent::__construct($catalogImage, $pageLayout, $catalogProductCompare, $wishlistData, $checkoutCart, $taxData, $catalogData, $coreData, $context, $data);
    }

    /**
     * Redefine default price block
     * Set current customer to tax calculation
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_block = 'Magento_Adminhtml_Block_Catalog_Product_Price';
        $this->_useLinkForAsLowAs = false;

        $taxCalculation = Mage::getSingleton('Magento_Tax_Model_Calculation');
        if (!$taxCalculation->getCustomer() && Mage::registry('current_customer')) {
            $taxCalculation->setCustomer(Mage::registry('current_customer'));
        }
    }

    /**
     * Retrieve product
     *
     * @return Magento_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', Mage::registry('product'));
        }
        $product = $this->getData('product');
        if (is_null($product->getTypeInstance()->getStoreFilter($product))) {
            $product->getTypeInstance()->setStoreFilter(Mage::app()->getStore($product->getStoreId()), $product);
        }

        return $product;
    }

    /**
     * Retrieve array of associated products
     *
     * @return array
     */
    public function getAssociatedProducts()
    {
        $product = $this->getProduct();
        $result = $product->getTypeInstance()
            ->getAssociatedProducts($product);

        $storeId = $product->getStoreId();
        foreach ($result as $item) {
            $item->setStoreId($storeId);
        }

        return $result;
    }


    /**
     * Set preconfigured values to grouped associated products
     *
     * @return Magento_Catalog_Block_Product_View_Type_Grouped
     */
    public function setPreconfiguredValue() {
        $configValues = $this->getProduct()->getPreconfiguredValues()->getSuperGroup();
        if (is_array($configValues)) {
            $associatedProducts = $this->getAssociatedProducts();
            foreach ($associatedProducts as $item) {
                if (isset($configValues[$item->getId()])) {
                    $item->setQty($configValues[$item->getId()]);
                }
            }
        }
        return $this;
    }

    /**
     * Check whether the price can be shown for the specified product
     *
     * @param Magento_Catalog_Model_Product $product
     * @return bool
     */
    public function getCanShowProductPrice($product)
    {
        return true;
    }

    /**
     * Checks whether block is last fieldset in popup
     *
     * @return bool
     */
    public function getIsLastFieldset()
    {
        $isLast = $this->getData('is_last_fieldset');
        if (!$isLast) {
            $options = $this->getProduct()->getOptions();
            return !$options || !count($options);
        }
        return $isLast;
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
        return $this->_coreData->currencyByStore($price, $store, false);
    }
}
