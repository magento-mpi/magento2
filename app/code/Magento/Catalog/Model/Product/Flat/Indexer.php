<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Product Flat Indexer Model
 *
 * @method \Magento\Catalog\Model\Resource\Product\Flat\Indexer _getResource()
 * @method \Magento\Catalog\Model\Resource\Product\Flat\Indexer getResource()
 * @method int getEntityTypeId()
 * @method \Magento\Catalog\Model\Product\Flat\Indexer setEntityTypeId(int $value)
 * @method int getAttributeSetId()
 * @method \Magento\Catalog\Model\Product\Flat\Indexer setAttributeSetId(int $value)
 * @method string getTypeId()
 * @method \Magento\Catalog\Model\Product\Flat\Indexer setTypeId(string $value)
 * @method string getSku()
 * @method \Magento\Catalog\Model\Product\Flat\Indexer setSku(string $value)
 * @method int getHasOptions()
 * @method \Magento\Catalog\Model\Product\Flat\Indexer setHasOptions(int $value)
 * @method int getRequiredOptions()
 * @method \Magento\Catalog\Model\Product\Flat\Indexer setRequiredOptions(int $value)
 * @method string getCreatedAt()
 * @method \Magento\Catalog\Model\Product\Flat\Indexer setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method \Magento\Catalog\Model\Product\Flat\Indexer setUpdatedAt(string $value)
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Product\Flat;

class Indexer extends \Magento\Core\Model\AbstractModel
{
    /**
     * Catalog product flat entity for indexers
     */
    const ENTITY = 'catalog_product_flat';

    /**
     * Indexers rebuild event type
     */
    const EVENT_TYPE_REBUILD = 'catalog_product_flat_rebuild';

    /**
     * Index indexer
     *
     * @var \Magento\Index\Model\Indexer
     */
    protected $_index;

    /**
     * Store manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Index\Model\Indexer $index
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Index\Model\Indexer $index,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_index = $index;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Standart model resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Catalog\Model\Resource\Product\Flat\Indexer');
    }

    /**
     * Rebuild Catalog Product Flat Data
     *
     * @param mixed $store
     * @return \Magento\Catalog\Model\Product\Flat\Indexer
     */
    public function rebuild($store = null)
    {
        if (is_null($store)) {
            $this->_getResource()->prepareFlatTables();
        } else {
            $this->_getResource()->prepareFlatTable($store);
        }
        $this->_index->processEntityAction(
            new \Magento\Object(array('id' => $store)),
            self::ENTITY,
            self::EVENT_TYPE_REBUILD
        );
        return $this;
    }

    /**
     * Update attribute data for flat table
     *
     * @param string $attributeCode
     * @param int $store
     * @param int|array $productIds
     * @return \Magento\Catalog\Model\Product\Flat\Indexer
     */
    public function updateAttribute($attributeCode, $store = null, $productIds = null)
    {
        if (is_null($store)) {
            foreach ($this->_storeManager->getStores() as $store) {
                $this->updateAttribute($attributeCode, $store->getId(), $productIds);
            }

            return $this;
        }

        $this->_getResource()->prepareFlatTable($store);
        $attribute = $this->_getResource()->getAttribute($attributeCode);
        $this->_getResource()->updateAttribute($attribute, $store, $productIds);
        $this->_getResource()->updateChildrenDataFromParent($store, $productIds);

        return $this;
    }

    /**
     * Prepare datastorage for catalog product flat
     *
     * @param int $store
     * @return \Magento\Catalog\Model\Product\Flat\Indexer
     */
    public function prepareDataStorage($store = null)
    {
        if (is_null($store)) {
            foreach ($this->_storeManager->getStores() as $store) {
                $this->prepareDataStorage($store->getId());
            }

            return $this;
        }

        $this->_getResource()->prepareFlatTable($store);

        return $this;
    }

    /**
     * Update events observer attributes
     *
     * @param int $store
     * @return \Magento\Catalog\Model\Product\Flat\Indexer
     */
    public function updateEventAttributes($store = null)
    {
        if (is_null($store)) {
            foreach ($this->_storeManager->getStores() as $store) {
                $this->updateEventAttributes($store->getId());
            }

            return $this;
        }

        $this->_getResource()->prepareFlatTable($store);
        $this->_getResource()->updateEventAttributes($store);
        $this->_getResource()->updateRelationProducts($store);

        return $this;
    }

    /**
     * Update product status
     *
     * @param int $productId
     * @param int $status
     * @param int $store
     * @return \Magento\Catalog\Model\Product\Flat\Indexer
     */
    public function updateProductStatus($productId, $status, $store = null)
    {
        if (is_null($store)) {
            foreach ($this->_storeManager->getStores() as $store) {
                $this->updateProductStatus($productId, $status, $store->getId());
            }
            return $this;
        }

        if ($status == \Magento\Catalog\Model\Product\Status::STATUS_ENABLED) {
            $this->_getResource()->updateProduct($productId, $store);
            $this->_getResource()->updateChildrenDataFromParent($store, $productId);
        } else {
            $this->_getResource()->removeProduct($productId, $store);
        }

        return $this;
    }

    /**
     * Update Catalog Product Flat data
     *
     * @param int|array $productIds
     * @param int $store
     * @return \Magento\Catalog\Model\Product\Flat\Indexer
     */
    public function updateProduct($productIds, $store = null)
    {
        if (is_null($store)) {
            foreach ($this->_storeManager->getStores() as $store) {
                $this->updateProduct($productIds, $store->getId());
            }
            return $this;
        }

        $resource = $this->_getResource();
        $resource->beginTransaction();
        try {
            $resource->removeProduct($productIds, $store);
            $resource->updateProduct($productIds, $store);
            $resource->updateRelationProducts($store, $productIds);
            $resource->commit();
        } catch (\Exception $e){
            $resource->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Save Catalog Product(s) Flat data
     *
     * @param int|array $productIds
     * @param int $store
     * @return \Magento\Catalog\Model\Product\Flat\Indexer
     */
    public function saveProduct($productIds, $store = null)
    {
        if (is_null($store)) {
            foreach ($this->_storeManager->getStores() as $store) {
                $this->saveProduct($productIds, $store->getId());
            }
            return $this;
        }

        $resource = $this->_getResource();
        $resource->beginTransaction();
        try {
            $resource->removeProduct($productIds, $store);
            $resource->saveProduct($productIds, $store);
            $resource->updateRelationProducts($store, $productIds);
            $resource->commit();
        } catch (\Exception $e){
            $resource->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Remove product from flat
     *
     * @param int|array $productIds
     * @param int $store
     * @return \Magento\Catalog\Model\Product\Flat\Indexer
     */
    public function removeProduct($productIds, $store = null)
    {
        if (is_null($store)) {
            foreach ($this->_storeManager->getStores() as $store) {
                $this->removeProduct($productIds, $store->getId());
            }
            return $this;
        }

        $this->_getResource()->removeProduct($productIds, $store);

        return $this;
    }

    /**
     * Delete store process
     *
     * @param int $store
     * @return \Magento\Catalog\Model\Product\Flat\Indexer
     */
    public function deleteStore($store)
    {
        $this->_getResource()->deleteFlatTable($store);
        return $this;
    }

    /**
     * Rebuild Catalog Product Flat Data for all stores
     *
     * @return \Magento\Catalog\Model\Product\Flat\Indexer
     */
    public function reindexAll()
    {
        $this->_getResource()->reindexAll();
        return $this;
    }

    /**
     * Retrieve list of attribute codes for flat
     *
     * @return array
     */
    public function getAttributeCodes()
    {
        return $this->_getResource()->getAttributeCodes();
    }
}
