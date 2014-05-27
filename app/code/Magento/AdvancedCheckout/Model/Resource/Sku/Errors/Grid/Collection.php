<?php
/**
 * SKU failed grid collection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Model\Resource\Sku\Errors\Grid;

use Magento\CatalogInventory\Service\V1\StockStatusServiceInterface as StockStatus;

/**
 * Class Collection
 */
class Collection extends \Magento\Framework\Data\Collection
{
    /**
     * @var \Magento\AdvancedCheckout\Model\Cart
     */
    protected $_cart;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_productModel;

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreHelper;

    /**
     * @var StockStatus
     */
    protected $stockStatus;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\AdvancedCheckout\Model\Cart $cart
     * @param \Magento\Catalog\Model\Product $productModel
     * @param \Magento\Core\Helper\Data $coreHelper
     * @param \Magento\CatalogInventory\Service\V1\StockStatusServiceInterface $stockStatusService
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\AdvancedCheckout\Model\Cart $cart,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Core\Helper\Data $coreHelper,
        StockStatus $stockStatusService
    ) {
        $this->_cart = $cart;
        $this->_productModel = $productModel;
        $this->_coreHelper = $coreHelper;
        $this->stockStatus = $stockStatusService;
        parent::__construct($entityFactory);
    }

    /**
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if (!$this->isLoaded()) {
            $parentBlock = $this->_cart;
            foreach ($parentBlock->getFailedItems() as $affectedItem) {
                // Escape user-submitted input
                if (isset($affectedItem['item']['qty'])) {
                    $affectedItem['item']['qty'] = empty($affectedItem['item']['qty'])
                        ? ''
                        : (float) $affectedItem['item']['qty'];
                }
                $item = new \Magento\Framework\Object();
                $item->setCode($affectedItem['code']);
                if (isset($affectedItem['error'])) {
                    $item->setError($affectedItem['error']);
                }
                $item->addData($affectedItem['item']);
                $item->setId($item->getSku());
                /* @var $product \Magento\Catalog\Model\Product */
                $product = $this->_productModel;
                if (isset($affectedItem['item']['id'])) {
                    $productId = $affectedItem['item']['id'];
                    $item->setProductId($productId);
                    $product->load($productId);
                    $status = $this->stockStatus->getProductStockStatus($productId, $this->getWebsiteId());
                    if (!empty($status[$productId])) {
                        $product->setIsSalable($status[$productId]);
                    }
                    $item->setPrice($this->_coreHelper->formatPrice($product->getPrice()));
                }
                $item->setProduct($product);
                $this->addItem($item);
            }
            $this->_setIsLoaded(true);
        }
        return $this;
    }

    /**
     * Get current website ID
     *
     * @return int|null|string
     */
    public function getWebsiteId()
    {
        return $this->_cart->getStore()->getWebsiteId();
    }
}
