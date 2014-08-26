<?php
/**
 * Flat item ereaser. Used to clear items from flat table
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Product\Flat\Action;

class Eraser
{
    /**
     * @var \Magento\Catalog\Helper\Product\Flat\Indexer
     */
    protected $productIndexerHelper;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Catalog\Helper\Product\Flat\Indexer $productHelper
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource,
        \Magento\Catalog\Helper\Product\Flat\Indexer $productHelper,
        \Magento\Framework\StoreManagerInterface $storeManager
    ) {
        $this->productIndexerHelper = $productHelper;
        $this->connection = $resource->getConnection('default');
        $this->storeManager = $storeManager;
    }

    /**
     * Remove products from flat that are not exist
     *
     * @param array $ids
     * @param int $storeId
     * @return void
     */
    public function removeDeletedProducts(array &$ids, $storeId)
    {
        $select = $this->connection->select()->from(
            $this->productIndexerHelper->getTable('catalog_product_entity')
        )->where(
            'entity_id IN(?)',
            $ids
        );
        $result = $this->connection->query($select);

        $existentProducts = array();
        foreach ($result->fetchAll() as $product) {
            $existentProducts[] = $product['entity_id'];
        }

        $productsToDelete = array_diff($ids, $existentProducts);
        $ids = $existentProducts;

        $this->deleteProductsFromStore($productsToDelete, $storeId);
    }

    /**
     * Delete products from flat table(s)
     *
     * @param int|array $productId
     * @param null|int $storeId
     * @return void
     */
    public function deleteProductsFromStore($productId, $storeId = null)
    {
        if (!is_array($productId)) {
            $productId = array($productId);
        }
        if (null === $storeId) {
            foreach ($this->storeManager->getStores() as $store) {
                $this->connection->delete(
                    $this->productIndexerHelper->getFlatTableName($store->getId()),
                    array('entity_id IN(?)' => $productId)
                );
            }
        } else {
            $this->connection->delete(
                $this->productIndexerHelper->getFlatTableName((int)$storeId),
                array('entity_id IN(?)' => $productId)
            );
        }
    }
}
