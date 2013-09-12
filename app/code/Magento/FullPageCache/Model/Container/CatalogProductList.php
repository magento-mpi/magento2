<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Placeholder container for catalog product lists
 */
class Magento_FullPageCache_Model_Container_CatalogProductList
    extends Magento_FullPageCache_Model_Container_Advanced_Quote
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_FullPageCache_Model_Cache $fpcCache
     * @param Magento_FullPageCache_Model_Container_Placeholder $placeholder
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_FullPageCache_Helper_Url $urlHelper
     */
    public function __construct(
        Magento_FullPageCache_Model_Cache $fpcCache,
        Magento_FullPageCache_Model_Container_Placeholder $placeholder,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_FullPageCache_Helper_Url $urlHelper
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($fpcCache, $placeholder, $urlHelper);
    }

    /**
     * Render block that was not cached
     *
     * @return false|string
     */
    protected function _renderBlock()
    {
        $productId = $this->_getProductId();
        if ($productId && !$this->_coreRegistry->registry('product')) {
            $product = Mage::getModel('Magento_Catalog_Model_Product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
            if ($product) {
                $this->_coreRegistry->register('product', $product);
            }
        }

        if ($this->_coreRegistry->registry('product')) {
            $block = $this->_getPlaceHolderBlock();
            Mage::dispatchEvent('render_block', array('block' => $block, 'placeholder' => $this->_placeholder));
            return $block->toHtml();
        }

        return '';
    }
}
