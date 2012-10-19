<?php
/**
 * Short description
 *
 * @copyright {}
 */
class Mage_Catalog_Webapi_ProductController extends Mage_Webapi_Controller_ActionAbstract
{
    /**
     * Create new product.
     *
     * @param Mage_Catalog_Webapi_Product_DataStructure $data
     * @return int ID of created product
     */
    public function createV1(Mage_Catalog_Webapi_Product_DataStructure $data)
    {
        return 1;
    }

    /**
     * Retrieve product data.
     *
     * @param int $productId ID of the product you want to retrieve.
     * @return Mage_Catalog_Webapi_Product_DataStructure
     */
    public function getV1($productId)
    {
        $product = new Mage_Catalog_Webapi_Product_DataStructure;
        $product->name = 'Fake product';
        $product->description = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit posuere.';
        $product->price = 9.99;
        $product->sku = 'product-001';
        $product->weight = 5.25;
        $stock = new Mage_Catalog_Webapi_Product_Stock_DataStructure;
        $stock->isInStock = true;
        $stock->quantity = 100;
        $product->stock = $stock;

        return $product;
    }
}
