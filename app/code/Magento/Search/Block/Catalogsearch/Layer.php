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
namespace Magento\Search\Block\Catalogsearch;

class Layer extends \Magento\CatalogSearch\Block\Layer
{
    /**
     * Search data
     *
     * @var Magento_Search_Helper_Data
     */
    protected $_searchData = null;

    /**
     * @param Magento_Search_Helper_Data $searchData
     * @param Magento_CatalogSearch_Helper_Data $catalogSearchData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Search_Helper_Data $searchData,
        Magento_CatalogSearch_Helper_Data $catalogSearchData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_searchData = $searchData;
        parent::__construct($catalogSearchData, $coreData, $context, $registry, $data);
    }

    /**
     * Initialize blocks names
     */
    protected function _initBlocks()
    {
        parent::_initBlocks();

        if ($this->_searchData->getIsEngineAvailableForNavigation(false)) {
            $this->_categoryBlockName        = 'Magento\Search\Block\Catalog\Layer\Filter\Category';
            $this->_attributeFilterBlockName = 'Magento\Search\Block\Catalogsearch\Layer\Filter\Attribute';
            $this->_priceFilterBlockName     = 'Magento\Search\Block\Catalog\Layer\Filter\Price';
            $this->_decimalFilterBlockName   = 'Magento\Search\Block\Catalog\Layer\Filter\Decimal';
        }
    }

    /**
     * Prepare child blocks
     *
     * @return \Magento\Search\Block\Catalog\Layer\View
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
     * @return \Magento\Catalog\Model\Layer
     */
    public function getLayer()
    {
        $helper = $this->_searchData;
        if ($helper->isThirdPartSearchEngine() && $helper->isActiveEngine()) {
            return \Mage::getSingleton('Magento\Search\Model\Search\Layer');
        }

        return parent::getLayer();
    }
}
