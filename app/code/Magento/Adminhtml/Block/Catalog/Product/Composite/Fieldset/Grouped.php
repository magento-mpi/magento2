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
namespace Magento\Adminhtml\Block\Catalog\Product\Composite\Fieldset;

class Grouped
    extends \Magento\Catalog\Block\Product\View\Type\Grouped
{
    /**
     * @var \Magento\Tax\Model\Calculation
     */
    protected $_taxCalculation;

    /**
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManger;

    /**
     * @param \Magento\Tax\Model\Calculation $taxCalculation
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Tax\Model\Calculation $taxCalculation,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
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

        $this->_block = 'Magento\Adminhtml\Block\Catalog\Product\Price';
        $this->_useLinkForAsLowAs = false;

        if (!$this->_taxCalculation->getCustomer() && $this->_coreRegistry->registry('current_customer')) {
            $this->_taxCalculation->setCustomer($this->_coreRegistry->registry('current_customer'));
        }
    }

    /**
     * Retrieve product
     *
     * @return \Magento\Catalog\Model\Product
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
     * @return \Magento\Catalog\Block\Product\View\Type\Grouped
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
     * @param \Magento\Catalog\Model\Product $product
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
        return $this->helper('Magento\Core\Helper\Data')->currencyByStore($price, $store, false);
    }
}
