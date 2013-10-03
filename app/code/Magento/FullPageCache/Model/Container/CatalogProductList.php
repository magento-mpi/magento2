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
namespace Magento\FullPageCache\Model\Container;

class CatalogProductList
    extends \Magento\FullPageCache\Model\Container\Advanced\Quote
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
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\FullPageCache\Model\Cache $fpcCache
     * @param \Magento\FullPageCache\Model\Container\Placeholder $placeholder
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\FullPageCache\Helper\Url $urlHelper
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\Layout $layout
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
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
        \Magento\Catalog\Model\ProductFactory $productFactory
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
