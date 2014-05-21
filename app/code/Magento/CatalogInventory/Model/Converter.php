<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Converts a StockItem Model to a Data Object
 */
namespace Magento\CatalogInventory\Model;

use Magento\CatalogInventory\Service\V1\Data\StockItemBuilder;
use Magento\CatalogInventory\Service\V1\Data\StockItem;
use Magento\CatalogInventory\Model\Stock\Item as StockItemModel;

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
     * Convert a customer model to a customer entity
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
