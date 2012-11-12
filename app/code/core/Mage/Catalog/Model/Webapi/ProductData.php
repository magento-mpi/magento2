<?php
/**
 * Data structure description for product entity
 *
 * @copyright {}
 */
class Mage_Catalog_Model_Webapi_ProductData
{
    /**
     * Product name
     *
     * @var string
     */
    public $name;

    /**
     * Product Description
     *
     * @var string
     */
    public $description;

    /**
     * Product SKU
     *
     * @var string
     */
    public $sku;

    /**
     * Product weight
     *
     * @var float
     */
    public $weight;

    /**
     * Product price
     *
     * @var float
     */
    public $price;

    /**
     * Product stock data
     *
     * @var Mage_Catalog_Model_Webapi_Product_StockData
     */
    public $stock = false;
}
