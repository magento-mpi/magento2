<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Layer\Filter;

/**
 * Layer attribute filter
 */
class Attribute extends \Magento\Catalog\Model\Layer\Filter\Attribute
{
    /**
     * Apply attribute option filter to product collection
     *
     * @param   \Magento\Framework\App\RequestInterface $request
     * @return  $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        $attributeValue = $request->getParam($this->_requestVar);
        if (empty($attributeValue)) {
            return $this;
        }
        $attribute = $this->getAttributeModel();
        $productCollection = $this->getLayer()->getProductCollection();
        $productCollection->applyFilterToCollection($attribute->getAttributeCode(), $attributeValue);
        return $this;
    }

    /**
     * Get data array for building attribute filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $attribute = $this->getAttributeModel();
        $this->_requestVar = $attribute->getAttributeCode();

        $productCollection = $this->getLayer()->getProductCollection();
        $optionsFacetedData = $productCollection->getFacetedData($attribute->getAttributeCode());

        $options = $attribute->getFrontend()->getSelectOptions();
        $optionsCount = $this->_getResource()->getCount($this);
        $data = array();
        foreach ($options as $option) {
            if (is_array($option['value'])) {
                continue;
            }
            if ($this->string->strlen($option['value'])) {
                // Check filter type
                if ($this->_getIsFilterableAttribute($attribute) == self::OPTIONS_ONLY_WITH_RESULTS) {
                    if (!empty($optionsCount[$option['value']])) {
                        $data[] = array(
                            'label' => $this->tagFilter->filter($option['label']),
                            'value' => $option['value'],
                            'count' => $optionsCount[$option['value']]
                        );
                    }
                } else {
                    $data[] = array(
                        'label' => $this->tagFilter->filter($option['label']),
                        'value' => $option['value'],
                        'count' => isset($optionsCount[$option['value']]) ? $optionsCount[$option['value']] : 0
                    );
                }
            }
        }

        return $data;
    }
}
