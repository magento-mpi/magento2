<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Model;

use Magento\CatalogInventory\Service\V1\Data\StockItemBuilder;
use Magento\CatalogInventory\Service\V1\Data\StockItem;
use Magento\CatalogInventory\Model\Stock\Item as StockItemModel;

/**
 * Converts a StockItem Model to a Data Object
 */
class Converter
{
    /**
     * @var StockItemBuilder
     */
    protected $stockItemBuilder;

    /**
     * @param StockItemBuilder $stockItemBuilder
     */
    public function __construct(StockItemBuilder $stockItemBuilder)
    {
        $this->stockItemBuilder = $stockItemBuilder;
    }

    /**
     * Converts a StockItem Model to a Data Object
     *
     * @param StockItemModel $stockItem
     * @return StockItem
     */
    public function createStockItemFromModel(StockItemModel $stockItem)
    {
        $this->stockItemBuilder->populateWithArray($stockItem->getData());
        return $this->stockItemBuilder->create();
    }
}
