<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TargetRule Product List Abstract Indexer Resource Model
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\TargetRule\Model\Resource\Index;

abstract class AbstractIndex extends \Magento\Core\Model\Resource\Db\AbstractDb
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
     * @param \Magento\Catalog\Model\Resource\Product $product
     * @param \Magento\Core\Model\Resource $resource
     */
    public function __construct(
        \Magento\Catalog\Model\Resource\Product $product,
        \Magento\Core\Model\Resource $resource
    ) {
        $this->_product = $product;
        parent::__construct($resource);
    }


    /**
     * Retrieve Product List Type identifier
     *
     * @throws \Magento\Core\Exception
     *
     * @return int
     */
    public function getListType()
    {
        if (is_null($this->_listType)) {
            throw new \Magento\Core\Exception(
                __('The product list type identifier is not defined.')
            );
        }
        return $this->_listType;
    }

    /**
     * Set Product List identifier
     *
     * @param int $listType
     * @return \Magento\TargetRule\Model\Resource\Index\AbstractIndex
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

    public function loadProductIdsBySegmentId($object, $segmentId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'product_ids')
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
        $value  = $this->_getReadAdapter()->fetchOne($select, $bind);

        return (!empty($value)) ? explode(',', $value) :array();
    }

    /**
     * Load Product Ids by Index object
     *
     * @param \Magento\TargetRule\Model\Index $object
     * @return array
     * @deprecated after 1.12.0.0
     */
    public function loadProductIds($object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'product_ids')
            ->where('entity_id = :entity_id')
            ->where('store_id = :store_id')
            ->where('customer_group_id = :customer_group_id');
        $bind = array(
            ':entity_id'         => $object->getProduct()->getEntityId(),
            ':store_id'          => $object->getStoreId(),
            ':customer_group_id' => $object->getCustomerGroupId()
        );
        $value  = $this->_getReadAdapter()->fetchOne($select, $bind);
        if (!empty($value)) {
            $productIds = explode(',', $value);
        } else {
            $productIds = array();
        }

        return $productIds;
    }

    /**
     * Save matched product Ids by customer segments
     *
     * @param \Magento\TargetRule\Model\Index $object
     * @param int $segmentId
     * @param string $productIds
     * @return \Magento\TargetRule\Model\Resource\Index\AbstractIndex
     */
    public function saveResultForCustomerSegments($object, $segmentId, $productIds)
    {
        $adapter = $this->_getWriteAdapter();
        $data    = array(
            'entity_id' => $object->getProduct()->getEntityId(),
            'store_id' => $object->getStoreId(),
            'customer_group_id' => $object->getCustomerGroupId(),
            'customer_segment_id' => $segmentId,
            'product_ids' => $productIds,
        );
        $adapter->insertOnDuplicate($this->getMainTable(), $data, array('product_ids'));
        return $this;
    }

    /**
     * Save matched product Ids
     *
     * @param \Magento\TargetRule\Model\Index $object
     * @param string $value
     * @return \Magento\TargetRule\Model\Resource\Index\AbstractIndex
     * @deprecated after 1.12.0.0
     */
    public function saveResult($object, $value)
    {
        $adapter = $this->_getWriteAdapter();
        $data    = array(
            'entity_id'         => $object->getProduct()->getEntityId(),
            'store_id'          => $object->getStoreId(),
            'customer_group_id' => $object->getCustomerGroupId(),
            'product_ids'       => $value
        );

        $adapter->insertOnDuplicate($this->getMainTable(), $data, array('product_ids'));

        return $this;
    }

    /**
     * Remove index by product ids
     *
     * @param \Magento\DB\Select|array $entityIds
     * @return \Magento\TargetRule\Model\Resource\Index\AbstractIndex
     */
    public function removeIndex($entityIds)
    {
        $this->_getWriteAdapter()->delete($this->getMainTable(), array(
            'entity_id IN(?)'   => $entityIds
        ));

        return $this;
    }

    /**
     * Remove all data from index
     *
     * @param \Magento\Core\Model\Store|int|array $store
     * @return \Magento\TargetRule\Model\Resource\Index\AbstractIndex
     */
    public function cleanIndex($store = null)
    {
        if (is_null($store)) {
            $this->_getWriteAdapter()->delete($this->getMainTable());
            return $this;
        }
        if ($store instanceof \Magento\Core\Model\Store) {
            $store = $store->getId();
        }
        $where = array('store_id IN(?)' => $store);
        $this->_getWriteAdapter()->delete($this->getMainTable(), $where);

        return $this;
    }
}
