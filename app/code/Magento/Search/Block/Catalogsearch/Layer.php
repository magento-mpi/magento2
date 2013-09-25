<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

 /**
 * Layered Navigation block for search
 *
 * @category    Magento
 * @package     Magento_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Search_Block_Catalogsearch_Layer extends Magento_CatalogSearch_Block_Layer
{
    /**
     * Search data
     *
     * @var Magento_Search_Helper_Data
     */
    protected $_searchData;

    /**
     * Extended search layer
     *
     * @var Magento_Search_Model_Search_Layer
     */
    protected $_searchLayer;

    /**
     * Construct
     * 
     * @param Magento_Search_Helper_Data $searchData
     * @param Magento_Search_Model_Search_Layer $searchLayer
     * @param Magento_CatalogSearch_Model_Layer $layer
     * @param Magento_CatalogSearch_Model_Resource_EngineProvider $engineProvider
     * @param Magento_CatalogSearch_Helper_Data $catalogSearchData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Search_Helper_Data $searchData,
        Magento_Search_Model_Search_Layer $searchLayer,
        Magento_CatalogSearch_Model_Layer $layer,
        Magento_CatalogSearch_Model_Resource_EngineProvider $engineProvider,
        Magento_CatalogSearch_Helper_Data $catalogSearchData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_searchData = $searchData;
        $this->_searchLayer = $searchLayer;
        parent::__construct(
            $layer, $engineProvider, $catalogSearchData, $coreData, $context, $registry, $data
        );
    }

    /**
     * Initialize blocks names
     */
    protected function _initBlocks()
    {
        parent::_initBlocks();

        if ($this->_searchData->getIsEngineAvailableForNavigation(false)) {
            $this->_categoryBlockName        = 'Magento_Search_Block_Catalog_Layer_Filter_Category';
            $this->_attributeFilterBlockName = 'Magento_Search_Block_Catalogsearch_Layer_Filter_Attribute';
            $this->_priceFilterBlockName     = 'Magento_Search_Block_Catalog_Layer_Filter_Price';
            $this->_decimalFilterBlockName   = 'Magento_Search_Block_Catalog_Layer_Filter_Decimal';
        }
    }

    /**
     * Prepare child blocks
     *
     * @return Magento_Search_Block_Catalog_Layer_View
     */
    protected function _prepareLayout()
    {
        if ($this->_searchData->isThirdPartSearchEngine()
            && $this->_searchData->getIsEngineAvailableForNavigation(false)
        ) {
            $stateBlock = $this->getLayout()->createBlock($this->_stateBlockName)
                ->setLayer($this->getLayer());

            $categoryBlock = $this->getLayout()->createBlock($this->_categoryBlockName)
                ->setLayer($this->getLayer())
                ->init();

            $filterableAttributes = $this->_getFilterableAttributes();
            $filters = array();
            foreach ($filterableAttributes as $attribute) {
                if ($attribute->getAttributeCode() == 'price') {
                    $filterBlockName = $this->_priceFilterBlockName;
                } elseif ($attribute->getBackendType() == 'decimal') {
                    $filterBlockName = $this->_decimalFilterBlockName;
                } else {
                    $filterBlockName = $this->_attributeFilterBlockName;
                }

                $filters[$attribute->getAttributeCode() . '_filter'] = $this->getLayout()->createBlock($filterBlockName)
                    ->setLayer($this->getLayer())
                    ->setAttributeModel($attribute)
                    ->init();
            }

            $this->setChild('layer_state', $stateBlock);
            $this->setChild('category_filter', $categoryBlock->addFacetCondition());

            foreach ($filters as $filterName => $block) {
                $this->setChild($filterName, $block->addFacetCondition());
            }

            $this->getLayer()->apply();
        } else {
            parent::_prepareLayout();
        }

        return $this;
    }

    /**
     * Check availability display layer block
     *
     * @return bool
     */
    public function canShowBlock()
    {
        if ($this->_searchData->isThirdPartSearchEngine() && $this->_searchData->isActiveEngine()) {
            return ($this->canShowOptions() || count($this->getLayer()->getState()->getFilters()));
        }
        return parent::canShowBlock();
    }

    /**
     * Get layer object
     *
     * @return Magento_Catalog_Model_Layer
     */
    public function getLayer()
    {
        if ($this->_searchData->isThirdPartSearchEngine() && $this->_searchData->isActiveEngine()) {
            return $this->_searchLayer;
        }

        return parent::getLayer();
    }
}
