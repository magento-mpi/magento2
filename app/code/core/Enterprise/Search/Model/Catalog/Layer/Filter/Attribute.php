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
class Enterprise_Search_Model_Catalog_Layer_Filter_Attribute extends Mage_Catalog_Model_Layer_Filter_Attribute
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

        $fieldName = Mage::helper('Enterprise_Search_Helper_Data')->getAttributeSolrFieldName($attribute);
        $productCollection = $this->getLayer()->getProductCollection();
        $options = $productCollection->getFacetedData($fieldName);
        ksort($options);

        $data = array();
        foreach ($options as $label => $count) {
            if (Mage::helper('Mage_Core_Helper_String')->strlen($label)) {
                // Check filter type
                if ($this->_getIsFilterableAttribute($attribute) == self::OPTIONS_ONLY_WITH_RESULTS) {
                    if (!empty($count)) {
                        $data[] = array(
                            'label' => $label,
                            'value' => $label,
                            'count' => $count,
                        );
                    }
                } else {
                    $data[] = array(
                        'label' => $label,
                        'value' => $label,
                        'count' => (int) $count,
                    );
                }
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
        $facetField = Mage::helper('Enterprise_Search_Helper_Data')->getAttributeSolrFieldName($this->getAttributeModel());
        $this->getLayer()->getProductCollection()->setFacetCondition($facetField);

        return $this;
    }

    /**
     * Apply attribute filter to solr query
     *
     * @param Mage_Catalog_Model_Layer_Filter_Attribute $filter
     * @param int $value
     */
    public function applyFilterToCollection($filter, $value)
    {
        if (empty($value)) {
            $value = array();
        } else if (!is_array($value)) {
            $value = array($value);
        }

        $productCollection = $this->getLayer()->getProductCollection();
        $attribute  = $filter->getAttributeModel();

        $param = Mage::helper('Enterprise_Search_Helper_Data')->getSearchParam($productCollection, $attribute, $value);
        $productCollection->addFqFilter($param);
        return $this;
    }
}
