<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogInventory\Block\Stockqty;

use Magento\Catalog\Model\Product;

/**
 * Product stock qty abstract block
 */
abstract class AbstractStockqty extends \Magento\Framework\View\Element\Template
{
    /**
     * Threshold qty config path
     */
    const XML_PATH_STOCK_THRESHOLD_QTY = 'cataloginventory/options/stock_threshold_qty';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\CatalogInventory\Service\V1\StockItemService
     */
    protected $stockItemService;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\CatalogInventory\Service\V1\StockItemService $stockItemService
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\CatalogInventory\Service\V1\StockItemService $stockItemService,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->stockItemService = $stockItemService;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve current product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    /**
     * Retrieve current product stock qty
     *
     * @return float
     */
    public function getStockQty()
    {
        if (!$this->hasData('product_stock_qty')) {
            $qty = 0;
            $productId = $this->getProduct()->getId();
            if ($productId) {
                $qty = $this->getProductStockQty($this->getProduct());
            }
            $this->setData('product_stock_qty', $qty);
        }
        return $this->getData('product_stock_qty');
    }

    /**
     * Retrieve product stock qty
     *
     * @param Product $product
     * @return float
     */
    public function getProductStockQty($product)
    {
        return $this->stockItemService->getStockQty($product->getId());
    }

    /**
     * Retrieve threshold of qty to display stock qty message
     *
     * @return string
     */
    public function getThresholdQty()
    {
        if (!$this->hasData('threshold_qty')) {
            $qty = (float) $this->_scopeConfig->getValue(
                self::XML_PATH_STOCK_THRESHOLD_QTY,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $this->setData('threshold_qty', $qty);
        }
        return $this->getData('threshold_qty');
    }

    /**
     * Retrieve id of message placeholder in template
     *
     * @return string
     */
    public function getPlaceholderId()
    {
        return 'stock-qty-' . $this->getProduct()->getId();
    }

    /**
     * Retrieve visibility of stock qty message
     *
     * @return bool
     */
    public function isMsgVisible()
    {
        return $this->getStockQty() > 0 && $this->getStockQty() <= $this->getThresholdQty();
    }
}
