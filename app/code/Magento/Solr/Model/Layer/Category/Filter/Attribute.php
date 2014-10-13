<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Model\Layer\Category\Filter;

/**
 * Layer attribute filter
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Attribute extends \Magento\Catalog\Model\Layer\Filter\Attribute
{
    /**
     * @var \Magento\Solr\Model\Resource\Solr\Engine
     */
    protected $_resourceEngine;

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Catalog\Model\Resource\Layer\Filter\AttributeFactory $filterAttributeFactory
     * @param \Magento\Framework\Stdlib\String $string
     * @param \Magento\Framework\Filter\StripTags $tagFilter
     * @param \Magento\Solr\Model\Resource\Solr\Engine $resourceEngine
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Resource\Layer\Filter\AttributeFactory $filterAttributeFactory,
        \Magento\Framework\Stdlib\String $string,
        \Magento\Framework\Filter\StripTags $tagFilter,
        \Magento\Solr\Model\Resource\Solr\Engine $resourceEngine,
        array $data = array()
    ) {
        $this->_resourceEngine = $resourceEngine;
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $filterAttributeFactory,
            $string,
            $tagFilter,
            $data
        );
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

        $fieldName = $this->_resourceEngine->getSearchEngineFieldName($attribute, 'nav');

        $productCollection = $this->getLayer()->getProductCollection();
        $optionsFacetedData = $productCollection->getFacetedData($fieldName);
        $options = $attribute->getSource()->getAllOptions(false);

        $data = array();
        foreach ($options as $option) {
            $optionId = $option['value'];
            // Check filter type
            if ($this->_getIsFilterableAttribute(
                $attribute
            ) != \Magento\Catalog\Model\Layer\Filter\Attribute::OPTIONS_ONLY_WITH_RESULTS ||
                !empty($optionsFacetedData[$optionId])
            ) {
                $data[] = array(
                    'label' => $this->tagFilter->filter($option['label']),
                    'value' => $option['label'],
                    'count' => isset($optionsFacetedData[$optionId]) ? $optionsFacetedData[$optionId] : 0
                );
            }
        }

        return $data;
    }

    /**
     * Apply attribute filter to layer
     *
     * @param \Zend_Controller_Request_Abstract $request
     * @return \Magento\Catalog\Model\Layer\Filter\Attribute
     */
    public function apply(\Zend_Controller_Request_Abstract $request)
    {
        $filter = $request->getParam($this->_requestVar);
        if (is_array($filter)) {
            return $this;
        }

        if ($filter && $this->_validateFilteredValue($filter)) {
            $this->applyFilterToCollection($this, $filter);
            $this->getLayer()->getState()->addFilter($this->_createItem($filter, $filter));
            $this->_items = array();
        }

        return $this;
    }

    /**
     * Validates if provided filter value is valid
     *
     * @param string $value
     * @return bool
     */
    protected function _validateFilteredValue($value)
    {
        $options = $this->getAttributeModel()->getSource()->getAllOptions();
        foreach ($options as $option) {
            if ($option['label'] == $value) {
                return true;
            }
        }
        return false;
    }

    /**
     * Add params to faceted search
     *
     * @return \Magento\Catalog\Model\Layer\Filter\Attribute
     */
    public function addFacetCondition()
    {
        $facetField = $this->_resourceEngine->getSearchEngineFieldName($this->getAttributeModel(), 'nav');
        $this->getLayer()->getProductCollection()->setFacetCondition($facetField);

        return $this;
    }

    /**
     * Apply attribute filter to solr query
     *
     * @param \Magento\Catalog\Model\Layer\Filter\Attribute $filter
     * @param mixed $value
     * @return \Magento\Catalog\Model\Layer\Filter\Attribute
     */
    public function applyFilterToCollection($filter, $value)
    {
        if (empty($value) || is_array(
            $value
        ) && isset(
            $value['from']
        ) && empty($value['from']) && isset(
            $value['to']
        ) && empty($value['to'])
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

        $fieldName = $this->_resourceEngine->getSearchEngineFieldName($attribute, 'nav');
        $this->getLayer()->getProductCollection()->addFqFilter(array($fieldName => $value));

        return $this;
    }

    /**
     * Retrieve number of items
     *
     * @return int
     */
    public function getItemsCount()
    {
        $attributeIsFilterable = $this->getAttributeModel()->getIsFilterable();
        if ($attributeIsFilterable == \Magento\Catalog\Model\Layer\Filter\Attribute::OPTIONS_ONLY_WITH_RESULTS) {
            return parent::getItemsCount();
        }

        $count = 0;
        foreach ($this->getItems() as $item) {
            if ($item->getCount()) {
                $count++;
            }
        }

        return $count;
    }
}
