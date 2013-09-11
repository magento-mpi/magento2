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
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Super\Config;

class Matrix
    extends \Magento\Backend\Block\Template
{
    /** @var \Magento\Core\Model\App */
    protected $_application;

    /** @var \Magento\Core\Model\LocaleInterface */
    protected $_locale;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\App $application
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\App $application,
        \Magento\Core\Model\LocaleInterface $locale,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_application = $application;
        $this->_locale = $locale;
    }

    /**
     * Retrieve price rendered according to current locale and currency settings
     *
     * @param int|float $price
     * @return string
     */
    public function renderPrice($price)
    {
        return $this->_locale->currency($this->_application->getBaseCurrencyCode())->toCurrency(sprintf('%f', $price));
    }

    /**
     * Get configurable product type
     *
     * @return \Magento\Catalog\Model\Product\Type\Configurable
     */
    protected function _getProductType()
    {
        return \Mage::getSingleton('Magento\Catalog\Model\Product\Type\Configurable');
    }

    /**
     * Retrieve currently edited product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return \Mage::registry('current_product');
    }

    /**
     * Retrieve all possible attribute values combinations
     *
     * @return array
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
     * @param $id
     *
     * @return string
     */
    public function getEditProductUrl($id)
    {
        return $this->getUrl('*/*/edit', array('id' => $id));
    }


    /**
     * Retrieve attributes data
     *
     * @return array
     */
    public function getAttributes()
    {
        if (!$this->hasData('attributes')) {
            $attributes = (array)$this->_getProductType()->getConfigurableAttributesAsArray($this->getProduct());
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
        return $this->_getProductType()->getUsedProductAttributes($this->getProduct());
    }

    /**
     * Retrieve actual list of associated products, array key is obtained from varying attributes values
     *
     * @return array
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
     * @return array
     */
    protected function _getAssociatedProducts()
    {
        $product = $this->getProduct();
        $ids = $this->getProduct()->getAssociatedProductIds();
        if ($ids === null) { // form data overrides any relations stored in database
            return $this->_getProductType()->getUsedProducts($product);
        }
        $products = array();
        foreach ($ids as $productId) {
            /** @var $product \Magento\Catalog\Model\Product */
            $product = \Mage::getModel('\Magento\Catalog\Model\Product')->load($productId);
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
        /** @var $config \Magento\Catalog\Model\Config */
        $config = \Mage::getSingleton('Magento\Catalog\Model\Config');
        /** @var $attribute \Magento\Catalog\Model\Resource\Eav\Attribute */
        $attribute = $config->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $code);
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
        return $this->getUrl('*/catalog_product_gallery/upload');
    }
}
