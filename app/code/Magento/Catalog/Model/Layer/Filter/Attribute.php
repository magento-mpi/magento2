<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layer attribute filter
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_Catalog_Model_Layer_Filter_Attribute extends Magento_Catalog_Model_Layer_Filter_Abstract
{
    const OPTIONS_ONLY_WITH_RESULTS = 1;

    /**
     * Resource instance
     *
     * @var Magento_Catalog_Model_Resource_Layer_Filter_Attribute
     */
    protected $_resource;

    /**
     * Core string
     *
     * @var Magento_Core_Helper_String
     */
    protected $_coreString = null;

    /**
     * Construct
     *
     * @param Magento_Catalog_Model_Layer_Filter_ItemFactory $filterItemFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Catalog_Model_Layer $catalogLayer
     * @param Magento_Catalog_Model_Resource_Layer_Filter_AttributeFactory $filterAttributeFactory
     * @param Magento_Core_Helper_String $coreString
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Model_Layer_Filter_ItemFactory $filterItemFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Catalog_Model_Layer $catalogLayer,
        Magento_Catalog_Model_Resource_Layer_Filter_AttributeFactory $filterAttributeFactory,
        Magento_Core_Helper_String $coreString,
        array $data = array()
    ) {
        $this->_resource = $filterAttributeFactory->create();
        $this->_coreString = $coreString;
        parent::__construct($filterItemFactory, $storeManager, $catalogLayer, $data);
        $this->_requestVar = 'attribute';
    }

    /**
     * Retrieve resource instance
     *
     * @return Magento_Catalog_Model_Resource_Layer_Filter_Attribute
     */
    protected function _getResource()
    {
        return $this->_resource;
    }

    /**
     * Get option text from frontend model by option id
     *
     * @param   int $optionId
     * @return  string|bool
     */
    protected function _getOptionText($optionId)
    {
        return $this->getAttributeModel()->getFrontend()->getOption($optionId);
    }

    /**
     * Apply attribute option filter to product collection
     *
     * @param   Zend_Controller_Request_Abstract $request
     * @param   Magento_Object $filterBlock
     * @return  Magento_Catalog_Model_Layer_Filter_Attribute
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $filter = $request->getParam($this->_requestVar);
        if (is_array($filter)) {
            return $this;
        }
        $text = $this->_getOptionText($filter);
        if ($filter && strlen($text)) {
            $this->_getResource()->applyFilterToCollection($this, $filter);
            $this->getLayer()->getState()->addFilter($this->_createItem($text, $filter));
            $this->_items = array();
        }
        return $this;
    }

    /**
     * Check whether specified attribute can be used in LN
     *
     * @param Magento_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return bool
     */
    protected function _getIsFilterableAttribute($attribute)
    {
        return $attribute->getIsFilterable();
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
            if ($this->_coreString->strlen($option['value'])) {
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

        return $data;
    }
}
