<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Model\Stock;

use Magento\CatalogInventory\Api\Data\StockStatusInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;

/**
 * CatalogInventory Stock Status
 * @package Magento\CatalogInventory\Model\Stock
 * @data-api
 *
 * @method Status setProductId(int $value)
 * @method Status setWebsiteId(int $value)
 * @method Status setStockId(int $value)
 * @method Status setQty(float $value)
 * @method Status setStockStatus(int $value)
 */
class Status extends \Magento\Framework\Model\AbstractModel implements StockStatusInterface
{
    /**#@+
     * Stock Status values
     */
    const STATUS_OUT_OF_STOCK = 0;

    const STATUS_IN_STOCK = 1;
    /**#@-*/

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param StockRegistryInterface $stockRegistry
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        StockRegistryInterface $stockRegistry,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->stockRegistry = $stockRegistry;
    }

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\CatalogInventory\Model\Resource\Stock\Status');
    }

    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->getData('product_id');
    }

    /**
     * @return int
     */
    public function getWebsiteId()
    {
        return $this->getData('website_id');
    }

    /**
     * @return int
     */
    public function getStockId()
    {
        return $this->getData('stock_id');
    }

    /**
     * @return int
     */
    public function getQty()
    {
        return $this->getData('qty');
    }

    /**
     * @return int
     */
    public function getStockStatus()
    {
        return $this->getData('stock_status');
    }

    /**
     * @return StockItemInterface
     */
    public function getStockItem()
    {
        return $this->stockRegistry->getStockItem($this->getProductId(), $this->getWebsiteId());
    }
}
