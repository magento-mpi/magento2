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
    protected $_searchData = null;

    /**
     * Construct
     *
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_CatalogSearch_Model_Layer $catalogSearchLayer
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_CatalogSearch_Helper_Data $catalogSearchData
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Search_Helper_Data $searchData
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_CatalogSearch_Model_Layer $catalogSearchLayer,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_CatalogSearch_Helper_Data $catalogSearchData,
        Magento_Core_Model_Registry $registry,
        Magento_Search_Helper_Data $searchData,
        array $data = array()
    ) {
        $this->_searchData = $searchData;
        parent::__construct($coreData, $context, $catalogSearchLayer, $storeManager, $catalogSearchData, $registry,
            $data);
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
        $helper = $this->_searchData;
        if ($helper->isThirdPartSearchEngine() && $helper->getIsEngineAvailableForNavigation(false)) {
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
        $helper = $this->_searchData;
        if ($helper->isThirdPartSearchEngine() && $helper->isActiveEngine()) {
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
        $helper = $this->_searchData;
        if ($helper->isThirdPartSearchEngine() && $helper->isActiveEngine()) {
            return Mage::getSingleton('Magento_Search_Model_Search_Layer');
        }

        return parent::getLayer();
    }
}
