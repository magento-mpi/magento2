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
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Catalog_Model_ProductFactory
     */
    protected $_productFactory;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_FullPageCache_Model_Cache $fpcCache
     * @param Magento_FullPageCache_Model_Container_Placeholder $placeholder
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_FullPageCache_Helper_Url $urlHelper
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Layout $layout
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_FullPageCache_Model_Cache $fpcCache,
        Magento_FullPageCache_Model_Container_Placeholder $placeholder,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_FullPageCache_Helper_Url $urlHelper,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Layout $layout,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Catalog_Model_ProductFactory $productFactory
    ) {
        parent::__construct(
            $eventManager, $fpcCache, $placeholder, $coreRegistry, $urlHelper, $coreStoreConfig, $layout
        );
        $this->_storeManager = $storeManager;
        $this->_productFactory = $productFactory;
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
            $product = $this->_productFactory
                ->create()
                ->setStoreId($this->_storeManager->getId())
                ->load($productId);
            if ($product) {
                $this->_coreRegistry->register('product', $product);
            }
        }

        if ($this->_coreRegistry->registry('product')) {
            $block = $this->_getPlaceHolderBlock();
            $this->_eventManager->dispatch('render_block', array(
                'block' => $block,
                'placeholder' => $this->_placeholder,
            ));
            return $block->toHtml();
        }

        return '';
    }
}
