<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface Stock
 */
interface StockInterface extends ExtensibleDataInterface
{
    const ID = 'stock_id';

    const WEBSITE_ID = 'website_id';

    const STOCK_NAME = 'stock_name';

    /**
     * Retrieve stock identifier
     *
     * @return int|null
     */
    public function getId();

    /**
     * Retrieve website identifier
     *
     * @return int
     */
    public function getWebsiteId();

    /**
     * Retrieve stock name
     *
     * @return string
     */
    public function getStockName();
}
