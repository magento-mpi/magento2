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
 */
class Magento_Adminhtml_Block_Catalog_Product_Composite_Fieldset_Grouped
    extends Magento_Catalog_Block_Product_View_Type_Grouped
{
    /**
     * @var Magento_Tax_Model_Calculation
     */
    protected $_taxCalculation;

    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManger;

    /**
     * @param Magento_Tax_Model_Calculation $taxCalculation
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Tax_Model_Calculation $taxCalculation,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_taxCalculation = $taxCalculation;
        $this->_storeManger = $storeManager;
        parent::__construct($coreRegistry, $taxData, $catalogData, $coreData, $context, $data);
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

        if (!$this->_taxCalculation->getCustomer() && $this->_coreRegistry->registry('current_customer')) {
            $this->_taxCalculation->setCustomer($this->_coreRegistry->registry('current_customer'));
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
            $this->setData('product', $this->_coreRegistry->registry('product'));
        }
        $product = $this->getData('product');
        if (is_null($product->getTypeInstance()->getStoreFilter($product))) {
            $product->getTypeInstance()->setStoreFilter(
                $this->_storeManger->getStore($product->getStoreId()),
                $product
            );
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
        return $this->helper('Magento_Core_Helper_Data')->currencyByStore($price, $store, false);
    }
}
