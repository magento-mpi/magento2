<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog inventory api
 */
class Magento_CatalogInventory_Model_Stock_Item_Api extends Magento_Catalog_Model_Api_Resource
{
    /**
     * Product model factory
     *
     * @var Magento_Catalog_Model_ProductFactory
     */
    protected $_productFactory;

    /**
     * Construct
     *
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     */
    public function __construct(Magento_Catalog_Model_ProductFactory $productFactory)
    {
        $this->_productFactory = $productFactory;
        $this->_storeIdSessionField = 'product_store_id';
    }

    public function items($productIds)
    {
        if (!is_array($productIds)) {
            $productIds = array($productIds);
        }

        /** @var \Magento_Catalog_Model_Product $product */
        $product = $this->_productFactory->create();

        foreach ($productIds as &$productId) {
            if ($newId = $product->getIdBySku($productId)) {
                $productId = $newId;
            }
        }

        $collection = $this->_productFactory->create()
            ->getCollection()
            ->setFlag('require_stock_items', true)
            ->addFieldToFilter('entity_id', array('in'=>$productIds));

        $result = array();

        foreach ($collection as $product) {
            if ($product->getStockItem()) {
                $result[] = array(
                    'product_id'    => $product->getId(),
                    'sku'           => $product->getSku(),
                    'qty'           => $product->getStockItem()->getQty(),
                    'is_in_stock'   => $product->getStockItem()->getIsInStock()
                );
            }
        }

        return $result;
    }
} // Class Magento_CatalogInventory_Model_Stock_Item_Api End
