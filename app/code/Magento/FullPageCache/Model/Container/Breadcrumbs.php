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
namespace Magento\FullPageCache\Model\Container;

class Breadcrumbs extends \Magento\FullPageCache\Model\Container\AbstractContainer
{
    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\FullPageCache\Model\Cache $fpcCache
     * @param \Magento\FullPageCache\Model\Container\Placeholder $placeholder
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\FullPageCache\Helper\Url $urlHelper
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\Layout $layout
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     */
    public function __construct(
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\FullPageCache\Model\Cache $fpcCache,
        \Magento\FullPageCache\Model\Container\Placeholder $placeholder,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\FullPageCache\Helper\Url $urlHelper,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\Layout $layout,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
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

        /** @var $product null|\Magento\Catalog\Model\Product */
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

        /** @var $breadcrumbsBlock \Magento\Page\Block\Html\Breadcrumbs */
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
