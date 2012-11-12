<?php
/**
 * Data structure description for product stock data
 *
 * @copyright {}
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
