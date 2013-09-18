<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layered Navigation block for search
 *
 */
namespace Magento\CatalogSearch\Block;

class Layer extends \Magento\Catalog\Block\Layer\View
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * Catalog search data
     *
     * @var \Magento\CatalogSearch\Helper\Data
     */
    protected $_catalogSearchData = null;

    /**
     * @param \Magento\CatalogSearch\Helper\Data $catalogSearchData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\CatalogSearch\Helper\Data $catalogSearchData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_catalogSearchData = $catalogSearchData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_coreRegistry->register('current_layer', $this->getLayer(), true);
    }

    /**
     * Initialize blocks names
     */
    protected function _initBlocks()
    {
        parent::_initBlocks();

        $this->_attributeFilterBlockName = 'Magento\CatalogSearch\Block\Layer\Filter\Attribute';
    }

    /**
     * Get layer object
     *
     * @return \Magento\Catalog\Model\Layer
     */
    public function getLayer()
    {
        return \Mage::getSingleton('Magento\CatalogSearch\Model\Layer');
    }

    /**
     * Check availability display layer block
     *
     * @return bool
     */
    public function canShowBlock()
    {
        $_isLNAllowedByEngine = $this->_catalogSearchData->getEngine()->isLayeredNavigationAllowed();
        if (!$_isLNAllowedByEngine) {
            return false;
        }
        $availableResCount = (int)\Mage::app()->getStore()
            ->getConfig(\Magento\CatalogSearch\Model\Layer::XML_PATH_DISPLAY_LAYER_COUNT);

        if (!$availableResCount || ($availableResCount > $this->getLayer()->getProductCollection()->getSize())) {
            return parent::canShowBlock();
        }
        return false;
    }
}
