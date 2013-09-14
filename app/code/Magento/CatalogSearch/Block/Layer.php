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
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        \Mage::register('current_layer', $this->getLayer(), true);
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
        $_isLNAllowedByEngine = \Mage::helper('Magento\CatalogSearch\Helper\Data')->getEngine()->isLayeredNavigationAllowed();
        if (!$_isLNAllowedByEngine) {
            return false;
        }
        $availableResCount = (int) \Mage::app()->getStore()
            ->getConfig(\Magento\CatalogSearch\Model\Layer::XML_PATH_DISPLAY_LAYER_COUNT);

        if (!$availableResCount
            || ($availableResCount > $this->getLayer()->getProductCollection()->getSize())) {
            return parent::canShowBlock();
        }
        return false;
    }
}
