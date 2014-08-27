<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TargetRule\Model\Resource\Index;

use Magento\TargetRule\Model\Index;
use Magento\Framework\Model\Exception as ModelException;

/**
 * TargetRule Product List Abstract Indexer Resource Model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class AbstractIndex extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Product List Type identifier
     *
     * @var int
     */
    protected $_listType;

    /**
     * @var \Magento\Catalog\Model\Resource\Product
     */
    protected $_product;

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Catalog\Model\Resource\Product $product
     */
    public function __construct(\Magento\Framework\App\Resource $resource, \Magento\Catalog\Model\Resource\Product $product)
    {
        $this->_product = $product;
        parent::__construct($resource);
    }

    /**
     * Retrieve Product List Type identifier
     *
     * @return int
     * @throws \Magento\Framework\Model\Exception
     */
    public function getListType()
    {
        if (is_null($this->_listType)) {
            throw new \Magento\Framework\Model\Exception(__('The product list type identifier is not defined.'));
        }
        return $this->_listType;
    }

    /**
     * Set Product List identifier
     *
     * @param int $listType
     * @return $this
     */
    public function setListType($listType)
    {
        $this->_listType = $listType;
        return $this;
    }

    /**
     * Retrieve Product Resource instance
     *
     * @return \Magento\Catalog\Model\Resource\Product
     */
    public function getProductResource()
    {
        return $this->_product;
    }

    /**
     * Retrieve Product Table Name
     *
     * @return string
     * @throws \Magento\Framework\Model\Exception
     */
    public function getProductTable()
    {
        if (empty($this->_mainTable)) {
            throw new ModelException(__('Empty main table name'));
        }
        return $this->getTable($this->_mainTable . '_product');
    }

    /**
     * @param Index $object
     * @param int $segmentId
     * @return array
     */
    public function loadProductIdsBySegmentId($object, $segmentId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('i' => $this->getMainTable()), array())
            ->joinInner(
                array('p' => $this->getProductTable()),
                'i.product_set_id = p.product_set_id',
                'product_id'
            )->where(
                'entity_id = :entity_id'
            )->where(
                'store_id = :store_id'
            )->where(
                'customer_group_id = :customer_group_id'
            )->where(
                'customer_segment_id = :customer_segment_id'
            );

        $bind = array(
            ':entity_id' => $object->getProduct()->getEntityId(),
            ':store_id' => $object->getStoreId(),
            ':customer_group_id' => $object->getCustomerGroupId(),
            ':customer_segment_id' => $segmentId
        );

        return $this->_getReadAdapter()->fetchCol($select, $bind);
    }

    /**
     * Clean products off the index
     *
     * @param int $productSetId
     * @return $this
     */
    public function deleteIndexProducts($productSetId)
    {
        $this->_getWriteAdapter()->delete($this->getProductTable(), array('product_set_id = ?' => $productSetId));

        return $this;
    }

    /**
     * Save product IDs for index
     *
     * @param int $productSetId
     * @param string|array $productIds
     * @return $this
     */
    public function saveIndexProducts($productSetId, $productIds)
    {
        if (is_string($productIds)) {
            $productIds = explode(',', $productIds);
        }

        if (count($productIds) > 0) {
            $data = array();
            foreach ($productIds as $productId) {
                $data[] = array(
                    'product_set_id' => $productSetId,
                    'product_id'    => $productId,
                );
            }
            $this->_getWriteAdapter()->insertMultiple($this->getProductTable(), $data);
        }

        return $this;
    }

    /**
     * Save matched product Ids by customer segments
     *
     * @param Index $object
     * @param int $segmentId
     * @param string $productIds
     * @return $this
     */
    public function saveResultForCustomerSegments($object, $segmentId, $productIds)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'product_set_id')
            ->where('entity_id = :entity_id')
            ->where('store_id = :store_id')
            ->where('customer_group_id = :customer_group_id')
            ->where('customer_segment_id = :customer_segment_id');

        $bind = array(
            ':entity_id' => $object->getProduct()->getEntityId(),
            ':store_id' => $object->getStoreId(),
            ':customer_group_id' => $object->getCustomerGroupId(),
            ':customer_segment_id' => $segmentId
        );

        $productSetId = $this->_getReadAdapter()->fetchOne($select, $bind);

        if (!$productSetId) {
            $data = array(
                'entity_id'           => $object->getProduct()->getEntityId(),
                'store_id'            => $object->getStoreId(),
                'customer_group_id'   => $object->getCustomerGroupId(),
                'customer_segment_id' => $segmentId,
            );
            $this->_getWriteAdapter()->insert($this->getMainTable(), $data);
            $productSetId = $this->_getWriteAdapter()->lastInsertId();
        } else {
            $this->deleteIndexProducts($productSetId);
        }
        $this->saveIndexProducts($productSetId, $productIds);

        return $this;
    }

    /**
     * Remove index by product ids
     *
     * @param \Magento\Framework\DB\Select|array $entityIds
     * @return $this
     */
    public function removeIndex($entityIds)
    {
        $this->_getWriteAdapter()->delete($this->getMainTable(), array('entity_id IN(?)' => $entityIds));

        return $this;
    }

    /**
     * Remove all data from index
     *
     * @param \Magento\Store\Model\Store|int|array|null $store
     * @return $this
     */
    public function cleanIndex($store = null)
    {
        if (is_null($store)) {
            $this->_getWriteAdapter()->delete($this->getMainTable());
            return $this;
        }
        if ($store instanceof \Magento\Store\Model\Store) {
            $store = $store->getId();
        }
        $where = array('store_id IN(?)' => $store);
        $this->_getWriteAdapter()->delete($this->getMainTable(), $where);

        return $this;
    }

    /**
     * Remove product from index table
     *
     * @param int|null $productId
     * @return $this
     */
    public function deleteProductFromIndex($productId = null)
    {
        if (!is_null($productId)) {
            $where = array('entity_id = ?' => $productId);
            $this->_getWriteAdapter()->delete($this->getMainTable(), $where);

            $where = array('product_id = ?' => $productId);
            $this->_getWriteAdapter()->delete($this->getProductTable(), $where);
        }
        return $this;
    }
}
