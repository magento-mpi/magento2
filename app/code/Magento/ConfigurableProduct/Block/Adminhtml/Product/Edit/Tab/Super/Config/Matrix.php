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
 * Product variations matrix block
 */
namespace Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tab\Super\Config;

use Magento\Catalog\Model\Product;

class Matrix
    extends \Magento\Backend\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    protected $_configurableType;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Catalog\Model\Config
     */
    protected $_config;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType
     * @param \Magento\Catalog\Model\Config $config
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType,
        \Magento\Catalog\Model\Config $config,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_configurableType = $configurableType;
        $this->_productFactory = $productFactory;
        $this->_config = $config;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve price rendered according to current locale and currency settings
     *
     * @param int|float $price
     * @return string
     */
    public function renderPrice($price)
    {
        return $this->_locale->currency($this->_app->getBaseCurrencyCode())->toCurrency(sprintf('%f', $price));
    }

    /**
     * Retrieve currently edited product object
     *
     * @return Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    /**
     * Retrieve all possible attribute values combinations
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getVariations()
    {
        $variationalAttributes = array();
        $usedProductAttributes = $this->getAttributes();
        foreach ($usedProductAttributes as $attribute) {
            $options = array();
            foreach ($attribute['options'] as $valueInfo) {
                foreach ($attribute['values'] as $priceData) {
                    if ($priceData['value_index'] == $valueInfo['value']
                        && (!isset($priceData['include']) || $priceData['include'])
                    ) {
                        $valueInfo['price'] = $priceData;
                        $options[] = $valueInfo;
                    }
                }
            }
            /** @var $attribute \Magento\Catalog\Model\Resource\Eav\Attribute */
            $variationalAttributes[] = array(
                'id' => $attribute['attribute_id'],
                'values' => $options,
            );
        }

        $attributesCount = count($variationalAttributes);
        if ($attributesCount === 0) {
            return array();
        }

        $variations = array();
        $currentVariation = array_fill(0, $attributesCount, 0);
        $variationalAttributes = array_reverse($variationalAttributes);
        $lastAttribute = $attributesCount - 1;
        do {
            for ($attributeIndex = 0; $attributeIndex < $attributesCount - 1; ++$attributeIndex) {
                if ($currentVariation[$attributeIndex] >= count($variationalAttributes[$attributeIndex]['values'])) {
                    $currentVariation[$attributeIndex] = 0;
                    ++$currentVariation[$attributeIndex + 1];
                }
            }
            if ($currentVariation[$lastAttribute] >= count($variationalAttributes[$lastAttribute]['values'])) {
                break;
            }

            $filledVariation = array();
            for ($attributeIndex = $attributesCount; $attributeIndex--;) {
                $currentAttribute = $variationalAttributes[$attributeIndex];
                $filledVariation[$currentAttribute['id']] =
                    $currentAttribute['values'][$currentVariation[$attributeIndex]];
            }

            $variations[] = $filledVariation;
            $currentVariation[0]++;
        } while (1);
        return $variations;
    }

    /**
     * Get url for product edit
     *
     * @param string $id
     * @return string
     */
    public function getEditProductUrl($id)
    {
        return $this->getUrl('catalog/*/edit', array('id' => $id));
    }

    /**
     * Retrieve attributes data
     *
     * @return array
     */
    public function getAttributes()
    {
        if (!$this->hasData('attributes')) {
            $attributes = (array)$this->_configurableType->getConfigurableAttributesAsArray($this->getProduct());
            $productData = (array)$this->getRequest()->getParam('product');
            if (isset($productData['configurable_attributes_data'])) {
                $configurableData = $productData['configurable_attributes_data'];
                foreach ($attributes as $key => &$attribute) {
                    if (isset($configurableData[$key])) {
                        $attribute['values'] = array_merge(
                            isset($attribute['values']) ? $attribute['values'] : array(),
                            isset($configurableData[$key]['values'])
                                ? array_filter($configurableData[$key]['values'])
                                : array()
                        );
                    }
                }
            }
            $this->setData('attributes', $attributes);
        }
        return $this->getData('attributes');
    }

    /**
     * Get used product attributes
     *
     * @return array
     */
    public function getUsedAttributes()
    {
        return $this->_configurableType->getUsedProductAttributes($this->getProduct());
    }

    /**
     * Retrieve actual list of associated products, array key is obtained from varying attributes values
     *
     * @return Product[]
     */
    public function getAssociatedProducts()
    {
        $productByUsedAttributes = array();
        foreach ($this->_getAssociatedProducts() as $product) {
            $keys = array();
            foreach ($this->getUsedAttributes() as $attribute) {
                /** @var $attribute \Magento\Catalog\Model\Resource\Eav\Attribute */
                $keys[] = $product->getData($attribute->getAttributeCode());
            }
            $productByUsedAttributes[implode('-', $keys)] = $product;
        }
        return $productByUsedAttributes;
    }

    /**
     * Retrieve actual list of associated products (i.e. if product contains variations matrix form data
     * - previously saved in database relations are not considered)
     *
     * @return Product[]
     */
    protected function _getAssociatedProducts()
    {
        $product = $this->getProduct();
        $ids = $this->getProduct()->getAssociatedProductIds();
        if ($ids === null) { // form data overrides any relations stored in database
            return $this->_configurableType->getUsedProducts($product);
        }
        $products = array();
        foreach ($ids as $productId) {
            /** @var $product Product */
            $product = $this->_productFactory->create()->load($productId);
            if ($product->getId()) {
                $products[] = $product;
            }
        }
        return $products;
    }

    /**
     * Get html class for attribute
     *
     * @param string $code
     * @return string
     */
    public function getAttributeFrontendClass($code)
    {
        /** @var $attribute \Magento\Catalog\Model\Resource\Eav\Attribute */
        $attribute = $this->_config->getAttribute(Product::ENTITY, $code);
        return $attribute instanceof \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
            ? $attribute->getFrontend()->getClass()
            : '';
    }

    /**
     * Get url to upload files
     *
     * @return string
     */
    public function getImageUploadUrl()
    {
        return $this->getUrl('catalog/product_gallery/upload');
    }
}
