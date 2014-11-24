<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogInventory\Model\Resource\Stock\Status;

use Magento\CatalogInventory\Api\Data\StockStatusCollectionInterface;
use Magento\Framework\Data\AbstractSearchResult;

/**
 * Class Collection
 * @package Magento\CatalogInventory\Model\Resource\Stock\Status
 */
class Collection extends AbstractSearchResult implements StockStatusCollectionInterface
{
    /**
     * @inheritdoc
     */
    protected function init()
    {
        $this->setDataInterfaceName('Magento\CatalogInventory\Api\Data\StockStatusInterface');
    }
}
