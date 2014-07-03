<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Model\Stock;

/**
 * Stock item registry
 */
class ItemRegistry extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Magento\CatalogInventory\Model\Stock\Item[]
     */
    protected $stockItemRegistry;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\ItemFactory
     */
    protected $stockItemFactory;

    /**
     * @var \Magento\CatalogInventory\Model\Resource\Stock\Item
     */
    protected $stockItemResource;

    /**
     * @param ItemFactory $stockItemFactory
     * @param \Magento\CatalogInventory\Model\Resource\Stock\Item $stockItemResource
     */
    public function __construct(
        ItemFactory $stockItemFactory,
        \Magento\CatalogInventory\Model\Resource\Stock\Item $stockItemResource
    ) {
        $this->stockItemFactory = $stockItemFactory;
        $this->stockItemResource = $stockItemResource;
    }

    /**
     * @param int $productId
     * @return \Magento\CatalogInventory\Model\Stock\Item
     */
    public function retrieve($productId)
    {
        if (empty($this->stockItemRegistry[$productId])) {
            /** @var \Magento\CatalogInventory\Model\Stock\Item $stockItem */
            $stockItem = $this->stockItemFactory->create();

            $this->stockItemResource->loadByProductId($stockItem, $productId);
            $this->stockItemRegistry[$productId] = $stockItem;
        }

        return $this->stockItemRegistry[$productId];
    }

    /**
     * @param int $productId
     * @return $this
     */
    public function erase($productId)
    {
        $this->stockItemRegistry[$productId] = null;
        return $this;
    }
}
