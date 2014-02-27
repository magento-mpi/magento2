<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Block\Catalogsearch;

 /**
  * Layered Navigation block for search
  *
  * @category    Magento
  * @package     Magento_Search
  * @author      Magento Core Team <core@magentocommerce.com>
  */
class Layer extends \Magento\CatalogSearch\Block\Layer
{
    /**
     * Search data
     *
     * @var \Magento\Search\Helper\Data
     */
    protected $_searchData;

    /**
     * Extended search layer
     *
     * @var \Magento\Search\Model\Search\Layer
     */
    protected $_searchLayer;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\CatalogSearch\Model\Layer $catalogLayer
     * @param \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider
     * @param \Magento\CatalogSearch\Helper\Data $catalogSearchData
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Search\Helper\Data $searchData
     * @param \Magento\Search\Model\Search\Layer $searchLayer
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\CatalogSearch\Model\Layer $catalogLayer,
        \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider,
        \Magento\CatalogSearch\Helper\Data $catalogSearchData,
        \Magento\Core\Model\Registry $registry,
        \Magento\Search\Helper\Data $searchData,
        \Magento\Search\Model\Search\Layer $searchLayer,
        array $data = array()
    ) {
        $this->_searchData = $searchData;
        $this->_searchLayer = $searchLayer;
        parent::__construct(
            $context,
            $catalogLayer,
            $engineProvider,
            $catalogSearchData,
            $registry,
            $data
        );
    }

    /**
     * Initialize blocks names
     *
     * @return void
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
     * @return $this
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
     * @return \Magento\Catalog\Model\Layer
     */
    public function getLayer()
    {
        if ($this->_searchData->isThirdPartSearchEngine() && $this->_searchData->isActiveEngine()) {
            return $this->_searchLayer;
        }

        return parent::getLayer();
    }
}
