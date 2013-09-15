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
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * Catalog search data
     *
     * @var Magento_CatalogSearch_Helper_Data
     */
    protected $_catalogSearchData = null;

    /**
     * @param Magento_CatalogSearch_Helper_Data $catalogSearchData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_CatalogSearch_Helper_Data $catalogSearchData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
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
        $availableResCount = (int)Mage::app()->getStore()
            ->getConfig(\Magento\CatalogSearch\Model\Layer::XML_PATH_DISPLAY_LAYER_COUNT);

        if (!$availableResCount || ($availableResCount > $this->getLayer()->getProductCollection()->getSize())) {
            return parent::canShowBlock();
        }
        return false;
    }
}
