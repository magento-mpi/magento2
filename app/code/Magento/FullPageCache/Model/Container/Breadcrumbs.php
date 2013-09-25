<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Breadcrumbs container
 */
class Magento_FullPageCache_Model_Container_Breadcrumbs extends Magento_FullPageCache_Model_Container_Abstract
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
     * @var Magento_Catalog_Model_CategoryFactory
     */
    protected $_categoryFactory;

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
     * @param Magento_Catalog_Model_CategoryFactory $categoryFactory
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
        Magento_Catalog_Model_ProductFactory $productFactory,
        Magento_Catalog_Model_CategoryFactory $categoryFactory
    ) {
        parent::__construct(
            $eventManager, $fpcCache, $placeholder, $coreRegistry, $urlHelper, $coreStoreConfig, $layout
        );
        $this->_storeManager = $storeManager;
        $this->_productFactory = $productFactory;
        $this->_categoryFactory = $categoryFactory;
    }

    /**
     * Get cache identifier
     *
     * @return string
     */
    protected function _getCacheId()
    {
        if ($this->_getCategoryId() || $this->_getProductId()) {
            $cacheSubKey = '_' . $this->_getCategoryId() . '_' . $this->_getProductId();
        } else {
            $cacheSubKey = $this->_getRequestId();
        }

        return 'CONTAINER_BREADCRUMBS_' . md5($this->_placeholder->getAttribute('cache_id') . $cacheSubKey);
    }

    /**
     * Render block content
     *
     * @return string
     */
    protected function _renderBlock()
    {
        $productId = $this->_getProductId();

        /** @var $product null|Magento_Catalog_Model_Product */
        $product = null;

        if ($productId) {
            $product = $this->_productFactory->create()
                ->setStoreId($this->_storeManager->getStore()->getId())
                ->load($productId);
            if ($product) {
                $this->_coreRegistry->register('current_product', $product);
            }
        }
        $categoryId = $this->_getCategoryId();

        if ($product !== null && !$product->canBeShowInCategory($categoryId)) {
            $categoryId = null;
            $this->_coreRegistry->unregister('current_category');
        }

        if ($categoryId && !$this->_coreRegistry->registry('current_category')) {
            $category = $this->_categoryFactory->create()->load($categoryId);
            if ($category) {
                $this->_coreRegistry->register('current_category', $category);
            }
        }

        //No need breadcrumbs on CMS pages
        if (!$productId && !$categoryId) {
            return '';
        }

        /** @var $breadcrumbsBlock Magento_Page_Block_Html_Breadcrumbs */
        $breadcrumbsBlock = $this->_getPlaceHolderBlock();
        $breadcrumbsBlock->setNameInLayout($this->_placeholder->getAttribute('name'));
        $crumbs = $this->_placeholder->getAttribute('crumbs');
        if ($crumbs) {
            $crumbs = unserialize(base64_decode($crumbs));
            foreach ($crumbs as $crumbName => $crumbInfo) {
                $breadcrumbsBlock->addCrumb($crumbName, $crumbInfo);
            }
        }

        $this->_eventManager->dispatch('render_block', array('block' => $breadcrumbsBlock, 'placeholder' => $this->_placeholder));
        return $breadcrumbsBlock->toHtml();
    }
}
