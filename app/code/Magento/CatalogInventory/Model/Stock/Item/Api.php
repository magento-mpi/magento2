<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog inventory api
 *
 * @category   Magento
 * @package    Magento_CatalogInventory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogInventory\Model\Stock\Item;

class Api extends \Magento\Catalog\Model\Api\Resource
{
    public function __construct()
    {
        $this->_storeIdSessionField = 'product_store_id';
    }

    public function items($productIds)
    {
        if (!is_array($productIds)) {
            $productIds = array($productIds);
        }

        $product = \Mage::getModel('Magento\Catalog\Model\Product');

        foreach ($productIds as &$productId) {
            if ($newId = $product->getIdBySku($productId)) {
                $productId = $newId;
            }
        }

        $collection = \Mage::getModel('Magento\Catalog\Model\Product')
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
} // Class \Magento\CatalogInventory\Model\Stock\Item\Api End
