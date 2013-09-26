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
 * Catalog layered navigation view block
 *
 * @category    Magento
 * @package     Magento_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Search\Block\Catalog\Layer;

class View extends \Magento\Catalog\Block\Layer\View
{
    /**
     * Extended search layer
     *
     * @var \Magento\Search\Model\Search\Layer
     */
    protected $_searchLayer;

    /**
     * Search data
     *
     * @var \Magento\Search\Helper\Data
     */
    protected $_searchData;

    /**
     * @param \Magento\Search\Helper\Data $searchData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Search\Model\Search\Layer $searchLayer
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Search\Helper\Data $searchData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Search\Model\Search\Layer $searchLayer,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_searchData = $searchData;
        $this->_searchLayer = $searchLayer;
        parent::__construct($layer, $coreData, $context, $data);
    }

    /**
     * Initialize blocks names
     */
    protected function _initBlocks()
    {
        parent::_initBlocks();

        if ($this->_searchData->getIsEngineAvailableForNavigation()) {
            $this->_categoryBlockName        = 'Magento\Search\Block\Catalog\Layer\Filter\Category';
            $this->_attributeFilterBlockName = 'Magento\Search\Block\Catalog\Layer\Filter\Attribute';
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
     * @return \Magento\Catalog\Model\Layer
     */
    public function getLayer()
    {
        if ($this->_searchData->getIsEngineAvailableForNavigation()) {
            return $this->_searchLayer;
        }

        return parent::getLayer();
    }
}
