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
 * Catalog layered navigation view block
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Block_Catalog_Layer_View extends Magento_Catalog_Block_Layer_View
{
    /**
     * Search data
     *
     * @var Enterprise_Search_Helper_Data
     */
    protected $_searchData = null;

    /**
     * @param Enterprise_Search_Helper_Data $searchData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Enterprise_Search_Helper_Data $searchData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_searchData = $searchData;
        parent::__construct($context, $data);
    }

    /**
     * Initialize blocks names
     */
    protected function _initBlocks()
    {
        parent::_initBlocks();

        if ($this->_searchData->getIsEngineAvailableForNavigation()) {
            $this->_categoryBlockName        = 'Enterprise_Search_Block_Catalog_Layer_Filter_Category';
            $this->_attributeFilterBlockName = 'Enterprise_Search_Block_Catalog_Layer_Filter_Attribute';
            $this->_priceFilterBlockName     = 'Enterprise_Search_Block_Catalog_Layer_Filter_Price';
            $this->_decimalFilterBlockName   = 'Enterprise_Search_Block_Catalog_Layer_Filter_Decimal';
        }
    }

    /**
     * Prepare child blocks
     *
     * @return Enterprise_Search_Block_Catalog_Layer_View
     */
    protected function _prepareLayout()
    {
        $helper = $this->_searchData;
        if ($helper->isThirdPartSearchEngine() && $helper->getIsEngineAvailableForNavigation()) {
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
     * Get layer object
     *
     * @return Magento_Catalog_Model_Layer
     */
    public function getLayer()
    {
        if ($this->_searchData->getIsEngineAvailableForNavigation()) {
            return Mage::getSingleton('Enterprise_Search_Model_Catalog_Layer');
        }

        return parent::getLayer();
    }
}
