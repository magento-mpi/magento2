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
     * @param ItemFactory $filterItemFactory
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Catalog\Model\Resource\Layer\Filter\AttributeFactory $filterAttributeFactory
     * @param \Magento\Framework\Stdlib\String $string
     * @param \Magento\Framework\Filter\StripTags $tagFilter
     * @param \Magento\Framework\Search\Request\Builder $requestBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Resource\Layer\Filter\AttributeFactory $filterAttributeFactory,
        \Magento\Framework\Stdlib\String $string,
        \Magento\Framework\Filter\StripTags $tagFilter,
        \Magento\Framework\Search\Request\Builder $requestBuilder,
        array $data = array()
    ) {
        parent::__construct(
            $filterItemFactory, $storeManager, $layer, $filterAttributeFactory, $string, $tagFilter, $data
        );
    }

    /**
     * Apply attribute option filter to product collection
     *
     * @param   \Zend_Controller_Request_Abstract $request
     * @return  $this
     */
    public function apply(\Zend_Controller_Request_Abstract $request)
    {
        $attributeValue = $request->getParam($this->_requestVar);
        if (empty($attributeValue)) {
            return $this;
        }
        $attribute = $this->getAttributeModel();
        $this->requestBuilder->bind($attribute->getAttributeCode(), $attributeValue);
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
