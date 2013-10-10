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
 * Adminhtml block for fieldset of configurable product
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Composite\Fieldset;

class Configurable extends \Magento\Catalog\Block\Product\View\Type\Configurable
{
    /**
     * Construct
     *
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Tax\Model\Calculation $taxCalculation
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Catalog\Helper\Product $catalogProduct
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Tax\Model\Calculation $taxCalculation,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        parent::__construct($storeManager, $catalogConfig, $taxCalculation, $coreRegistry, $catalogProduct, $taxData,
            $catalogData, $coreData, $context, $data);
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
                $this->_storeConfig->getStore($product->getStoreId()),
                $product
            );
        }

        return $product;
    }

    /**
     * Retrieve current store
     *
     * @return \Magento\Core\Model\Store
     */
    public function getCurrentStore()
    {
        return $this->_storeManager->getStore($this->getProduct()->getStoreId());
    }

    /**
     * Returns additional values for js config, con be overriden by descedants
     *
     * @return array
     */
    protected function _getAdditionalConfig()
    {
        $result = parent::_getAdditionalConfig();
        $result['disablePriceReload'] = true; // There's no field for price at popup
        $result['stablePrices'] = true; // We don't want to recalc prices displayed in OPTIONs of SELECT
        return $result;
    }
}
