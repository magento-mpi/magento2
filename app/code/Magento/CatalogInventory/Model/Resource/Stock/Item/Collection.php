<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogInventory\Model\Resource\Stock\Item;

use Magento\CatalogInventory\Api\Data\StockItemCollectionInterface;
use Magento\Framework\Data\AbstractSearchResult;

/**
 * Class Collection
 * @package Magento\CatalogInventory\Model\Resource\Stock\Item
 */
class Collection extends AbstractSearchResult implements StockItemCollectionInterface
{
    /**
     * @inheritdoc
     */
    protected function init()
    {
        $this->setDataInterfaceName('Magento\CatalogInventory\Api\Data\StockItemInterface');
    }
}
