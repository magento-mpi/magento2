<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product variations matrix block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Matrix
    extends Mage_Backend_Block_Template
{
    /**
     * Get configurable product type
     *
     * @return Mage_Catalog_Model_Product_Type_Configurable
     */
    protected function _getProductType()
    {
        return Mage::getModel('Mage_Catalog_Model_Product_Type_Configurable');
    }

    /**
     * Retrieve currently edited product object
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        return Mage::registry('current_product');
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
        foreach ($usedProductAttributes as &$attribute) {
            $options = array();
            foreach ($attribute['options'] as $key => $valueInfo) {
                foreach ($attribute['values'] as $priceData) {
                    if ($priceData['value_index'] == $valueInfo['value']
                        && (!isset($priceData['include']) || $priceData['include'])
                    ) {
                        $valueInfo['price'] = $priceData;
                        $options[] = $valueInfo;
                    }
                }
            }
            /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
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
            $attributes = (array)$this->_getProductType()->getConfigurableAttributesAsArray($this->_getProduct());
            $productData = (array)$this->getRequest()->getParam('product');
            if (isset($productData['configurable_attributes_data'])) {
                $configurableData = $productData['configurable_attributes_data'];
                foreach ($attributes as $key => &$attribute) {
                    if (isset($configurableData[$key])) {
                        $attribute['values'] = array_merge(
                            isset($attribute['values']) ? $attribute['values'] : array(),
                            isset($configurableData[$key]['values']) ? array_filter($configurableData[$key]['values']) : array()
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
        return $this->_getProductType()->getUsedProductAttributes($this->_getProduct());
    }

    /**
     * Get used products
     *
     * @return array
     */
    public function getUsedProducts()
    {
        $productByUsedAttributes = array();
        foreach ($this->_getProductType()->getUsedProducts($this->_getProduct()) as $product) {
            $keys = array();
            foreach ($this->getUsedAttributes() as $attribute) {
                /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
                $keys[] = $product->getData($attribute->getAttributeCode());
            }

            $productByUsedAttributes[implode('-', $keys)] = $product;
        }
        return $productByUsedAttributes;
    }

    /**
     * Get html class for attribute
     *
     * @param string $code
     * @return string
     */
    public function getAttributeClassName($code)
    {
        /** @var $config Mage_Catalog_Model_Config */
        $config = Mage::getSingleton('Mage_Catalog_Model_Config');
        /** @var $attribute Mage_Eav_Model_Entity_Attribute_Abstract */
        $attribute = $config->getAttribute(Mage_Catalog_Model_Product::ENTITY, $code);
        if (!$attribute instanceof Mage_Eav_Model_Entity_Attribute_Abstract) {
            return '';
        }
        return $attribute->getFrontend()->getClass();
    }
}
