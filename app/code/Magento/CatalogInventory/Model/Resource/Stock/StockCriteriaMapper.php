<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogInventory\Model\Resource\Stock;

use Magento\Framework\DB\GenericMapper;

/**
 * Class StockCriteriaMapper
 * @package Magento\CatalogInventory\Model\Resource\Stock
 */
class StockCriteriaMapper extends GenericMapper
{
    /**
     * @inheritdoc
     */
    protected function init()
    {
        $this->initResource('Magento\CatalogInventory\Model\Resource\Stock');
    }
}
