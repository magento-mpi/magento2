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
namespace Magento\GroupedProduct\Block\Adminhtml\Product\Composite\Fieldset;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Grouped extends \Magento\GroupedProduct\Block\Product\View\Type\Grouped
{
    /**
     * @var \Magento\Catalog\Helper\Product\Price
     */
    protected $priceHelper;

    /**
     * @var string
     */
    protected $_priceBlockDefaultTemplate = 'catalog/product/price.phtml';

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreHelper;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Stdlib\ArrayUtils $arrayUtils
     * @param \Magento\Catalog\Helper\Product\Price $priceHelper
     * @param \Magento\Core\Helper\Data $coreHelper
     * @param array $data
     * @param array $priceBlockTypes
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Stdlib\ArrayUtils $arrayUtils,
        \Magento\Catalog\Helper\Product\Price $priceHelper,
        \Magento\Core\Helper\Data $coreHelper,
        array $data = array(),
        array $priceBlockTypes = array()
    ) {
        $this->_coreHelper = $coreHelper;
        $this->priceHelper = $priceHelper;
        parent::__construct(
            $context,
            $arrayUtils,
            $data,
            $priceBlockTypes
        );
    }

    /**
     * Redefine default price block
     * Set current customer to tax calculation
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_block = 'Magento\Catalog\Block\Adminhtml\Product\Price';
        $this->_useLinkForAsLowAs = false;

        if (!$this->priceHelper->getCustomer() && $this->_coreRegistry->registry('current_customer')) {
            $this->priceHelper->setCustomer($this->_coreRegistry->registry('current_customer'));
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
                $this->_storeManager->getStore($product->getStoreId()),
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
        $result = $product->getTypeInstance()->getAssociatedProducts($product);

        $storeId = $product->getStoreId();
        foreach ($result as $item) {
            $item->setStoreId($storeId);
        }

        return $result;
    }

    /**
     * Set preconfigured values to grouped associated products
     *
     * @return $this
     */
    public function setPreconfiguredValue()
    {
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
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getCanShowProductPrice($product)
    {
        return true;
    }

    /**
     * Checks whether block is last fieldset in popup
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
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
        return $this->_coreHelper->currencyByStore($price, $store, false);
    }
}
