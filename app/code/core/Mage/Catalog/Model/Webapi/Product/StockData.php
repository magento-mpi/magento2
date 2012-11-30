<?php
/**
 * Data structure description for product stock data
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Catalog_Model_Webapi_Product_StockData
{
    /**
     * Manage stock or not.
     *
     * @var bool
     */
    public $manageStock = true;

    /**
     * Quantity of a product.
     *
     * @var float
     */
    public $quantity;

    /**
     * Is product in stock?
     *
     * @var bool
     */
    public $isInStock;
}
