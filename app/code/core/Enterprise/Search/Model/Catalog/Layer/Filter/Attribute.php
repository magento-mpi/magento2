<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

        $key = $this->getLayer()->getStateKey().'_'.$this->_requestVar;
        $data = $this->getLayer()->getAggregator()->getCacheData($key);

        if ($data === null) {
            $options = $attribute->getFrontend()->getSelectOptions();
            $optionsCount = $this->_getResource()->getCount($this);

            $data = array();

            foreach ($options as $option) {
                if (is_array($option['value'])) {
                    continue;
                }

                if (Mage::helper('core/string')->strlen($option['value'])) {
                    // Check filter type
                    if ($this->_getIsFilterableAttribute($attribute) == self::OPTIONS_ONLY_WITH_RESULTS) {
                        if (!empty($optionsCount[$option['value']])) {
                            $data[] = array(
                                'label' => $option['label'],
                                'value' => $option['value'],
                                'count' => $optionsCount[$option['value']],
                            );
                        }
                    }
                    else {
                        $data[] = array(
                            'label' => $option['label'],
                            'value' => $option['value'],
                            'count' => isset($optionsCount[$option['value']]) ? $optionsCount[$option['value']] : 0,
                        );
                    }
                }
            }

            $tags = array(
                Mage_Eav_Model_Entity_Attribute::CACHE_TAG .':'. $attribute->getId()
            );

            $tags = $this->getLayer()->getStateTags($tags);
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
        return $data;
    }

    /**
     * Retrieve count products for attribute filter
     *
     * @param object $attribute
     *
     * @return array
     */
    public function getCount($attribute)
    {
        $attribute = $attribute->getAttributeModel();
        $params = array();

        $params['facet'] = array(
            'field'  => $this->getAttributeSolrFieldName($attribute),
            'values' => array()
        );

        $productCollection = $this->getLayer()->getProductCollection();
        $facets = $productCollection->getFacets($params);

        $facet = !empty($facets[$params['facet']['field']]) ? $facets[$params['facet']['field']] : array();

        $resultFacet = array();
        $options = $attribute->getFrontend()->getSelectOptions();
        foreach ($options as $option) {
            $optionLabel = $this->_prepareOptionLabel($option['label']);
            if (isset($facet[$optionLabel])) {
                $resultFacet[$option['value']]=$facet[$optionLabel];
            }
        }

        return $resultFacet;
    }

    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $filter = $request->getParam($this->_requestVar);
        if (is_array($filter)) {
            return $this;
        }

        $text = $this->_getOptionText($filter);
        if ($filter && $text) {
            $this->_getResource()->applyFilterToCollection($this, $filter);
            $this->getLayer()->getState()->addFilter($this->_createItem($text, $filter));
            $this->_items = array();
        }

        $facetField = $this->_getResource()->getAttributeSolrFieldName($this->getAttributeModel());

        $productCollection = $this->getLayer()->getProductCollection();
        $productCollection->setFacetCondition($facetField);

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

        $productCollection = Mage::getSingleton('catalog/layer')->getProductCollection();
        $attribute  = $filter->getAttributeModel();

        $param = $this->_getSearchParam($productCollection, $attribute, $value);
        $productCollection->addSearchQfFilter($param);

        return $this;
    }

    /**
     * Retrieve resource model
     *
     * @return object
     */
    protected function _getResource()
    {
        $engineClassName         = get_class(Mage::helper('catalogsearch')->getEngine());
        $fulltextEngineClassName = get_class(Mage::getResourceSingleton('catalogsearch/fulltext_engine'));

        if ($engineClassName == $fulltextEngineClassName) {
            return parent::_getResource();
        }

        return Mage::getResourceSingleton('enterprise_search/catalog_facets_attribute');
    }
}
