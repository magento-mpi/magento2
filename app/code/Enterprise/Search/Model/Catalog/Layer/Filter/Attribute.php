<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layer attribute filter
 *
 * @category   Enterprise
 * @package    Enterprise_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Model_Catalog_Layer_Filter_Attribute extends Magento_Catalog_Model_Layer_Filter_Attribute
{
    /**
     * Get data array for building attribute filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $attribute = $this->getAttributeModel();
        $this->_requestVar = $attribute->getAttributeCode();

        $engine = Mage::getResourceSingleton('Enterprise_Search_Model_Resource_Engine');
        $fieldName = $engine->getSearchEngineFieldName($attribute, 'nav');

        $productCollection = $this->getLayer()->getProductCollection();
        $optionsFacetedData = $productCollection->getFacetedData($fieldName);
        $options = $attribute->getSource()->getAllOptions(false);

        $data = array();
        foreach ($options as $option) {
            $optionId = $option['value'];
            // Check filter type
            if ($this->_getIsFilterableAttribute($attribute) != self::OPTIONS_ONLY_WITH_RESULTS
                || !empty($optionsFacetedData[$optionId])
            ) {
                $data[] = array(
                    'label' => $option['label'],
                    'value' => $option['label'],
                    'count' => isset($optionsFacetedData[$optionId]) ? $optionsFacetedData[$optionId] : 0,
                );
            }
        }

        return $data;
    }

    /**
     * Apply attribute filter to layer
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param object $filterBlock
     * @return Enterprise_Search_Model_Catalog_Layer_Filter_Attribute
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $filter = $request->getParam($this->_requestVar);
        if (is_array($filter)) {
            return $this;
        }

        if ($filter) {
            $this->applyFilterToCollection($this, $filter);
            $this->getLayer()->getState()->addFilter($this->_createItem($filter, $filter));
            $this->_items = array();
        }

        return $this;
    }

    /**
     * Add params to faceted search
     *
     * @return Enterprise_Search_Model_Catalog_Layer_Filter_Attribute
     */
    public function addFacetCondition()
    {
        $engine = Mage::getResourceSingleton('Enterprise_Search_Model_Resource_Engine');
        $facetField = $engine->getSearchEngineFieldName($this->getAttributeModel(), 'nav');
        $this->getLayer()->getProductCollection()->setFacetCondition($facetField);

        return $this;
    }

    /**
     * Apply attribute filter to solr query
     *
     * @param   Magento_Catalog_Model_Layer_Filter_Attribute $filter
     * @param   mixed $value
     *
     * @return  Enterprise_Search_Model_Catalog_Layer_Filter_Attribute
     */
    public function applyFilterToCollection($filter, $value)
    {
        if (empty($value) || (is_array($value) && isset($value['from']) && empty($value['from'])
            && isset($value['to']) && empty($value['to']))
        ) {
            $value = array();
        }

        if (!is_array($value)) {
            $value = array($value);
        }

        $attribute = $filter->getAttributeModel();
        $options = $attribute->getSource()->getAllOptions();
        foreach ($value as &$valueText) {
            foreach ($options as $option) {
                if ($option['label'] == $valueText) {
                    $valueText = $option['value'];
                }
            }
        }

        $fieldName = Mage::getResourceSingleton('Enterprise_Search_Model_Resource_Engine')
            ->getSearchEngineFieldName($attribute, 'nav');
        $this->getLayer()->getProductCollection()->addFqFilter(array($fieldName => $value));

        return $this;
    }
}
