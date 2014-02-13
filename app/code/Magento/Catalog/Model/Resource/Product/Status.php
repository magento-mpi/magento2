<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Resource\Product;

use Magento\Core\Model\Config\Element;

/**
 * Catalog product website resource model
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Status extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Product atrribute cache
     *
     * @var array
     */
    protected $_productAttributes  = array();

    /**
     * Catalog product
     *
     * @var \Magento\Catalog\Model\Product
     */
    protected $_catalogProduct;

    /**
     * Store manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Catalog product1
     *
     * @var \Magento\Catalog\Model\Resource\Product
     */
    protected $_productResource;

    /**
     * @param \Magento\App\Resource $resource
     * @param \Magento\Catalog\Model\Resource\Product $productResource
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\App\Resource $resource,
        \Magento\Catalog\Model\Resource\Product $productResource,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->_productResource = $productResource;
        $this->_storeManager = $storeManager;
        parent::__construct($resource);
    }

    /**
     * Initialize connection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('catalog_product_enabled_index', 'product_id');
    }

    /**
     * Retrieve product attribute (public method for status model)
     *
     * @param string $attributeCode
     * @return \Magento\Catalog\Model\Resource\Eav\Attribute
     */
    public function getProductAttribute($attributeCode)
    {
        return $this->_getProductAttribute($attributeCode);
    }

    /**
     * Retrieve product attribute
     *
     * @param string|integer|Element $attribute
     * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     */
    protected function _getProductAttribute($attribute)
    {
        if (empty($this->_productAttributes[$attribute])) {
            $this->_productAttributes[$attribute] = $this->_productResource->getAttribute($attribute);
        }
        return $this->_productAttributes[$attribute];
    }

    /**
     * Refresh enabled index cache
     *
     * @param int $productId
     * @param int $storeId
     * @return $this
     */
    public function refreshEnabledIndex($productId, $storeId)
    {
        if ($storeId == \Magento\Core\Model\Store::DEFAULT_STORE_ID) {
            foreach ($this->_storeManager->getStores() as $store) {
                $this->refreshEnabledIndex($productId, $store->getId());
            }

            return $this;
        }

        $this->_productResource->refreshEnabledIndex($storeId, $productId);

        return $this;
    }

    /**
     * Update product status for store
     *
     * @param int $productId
     * @param int $storeId
     * @param int $value
     * @return $this
     */
    public function updateProductStatus($productId, $storeId, $value)
    {
        $statusAttributeId  = $this->_getProductAttribute('status')->getId();
        $statusEntityTypeId = $this->_getProductAttribute('status')->getEntityTypeId();
        $statusTable        = $this->_getProductAttribute('status')->getBackend()->getTable();
        $refreshIndex       = true;
        $adapter            = $this->_getWriteAdapter();

        $data = new \Magento\Object(array(
            'entity_type_id' => $statusEntityTypeId,
            'attribute_id'   => $statusAttributeId,
            'store_id'       => $storeId,
            'entity_id'      => $productId,
            'value'          => $value
        ));

        $data = $this->_prepareDataForTable($data, $statusTable);

        $select = $adapter->select()
            ->from($statusTable)
            ->where('attribute_id = :attribute_id')
            ->where('store_id     = :store_id')
            ->where('entity_id    = :product_id');

        $row = $adapter->fetchRow($select);

        if ($row) {
            if ($row['value'] == $value) {
                $refreshIndex = false;
            } else {
                $condition = array('value_id = ?' => $row['value_id']);
                $adapter->update($statusTable, $data, $condition);
            }
        } else {
            $adapter->insert($statusTable, $data);
        }

        if ($refreshIndex) {
            $this->refreshEnabledIndex($productId, $storeId);
        }

        return $this;
    }

    /**
     * Retrieve Product(s) status for store
     * Return array where key is a product_id, value - status
     *
     * @param array|int $productIds
     * @param int $storeId
     * @return array
     */
    public function getProductStatus($productIds, $storeId = null)
    {
        $statuses = array();

        $attribute      = $this->_getProductAttribute('status');
        $attributeTable = $attribute->getBackend()->getTable();
        $adapter        = $this->_getReadAdapter();

        if (!is_array($productIds)) {
            $productIds = array($productIds);
        }

        if ($storeId === null || $storeId == \Magento\Core\Model\Store::DEFAULT_STORE_ID) {
            $select = $adapter->select()
                ->from($attributeTable, array('entity_id', 'value'))
                ->where('entity_id IN (?)', $productIds)
                ->where('attribute_id = ?', $attribute->getAttributeId())
                ->where('store_id = ?', \Magento\Core\Model\Store::DEFAULT_STORE_ID);

            $rows = $adapter->fetchPairs($select);
        } else {
            $valueCheckSql = $adapter->getCheckSql('t2.value_id > 0', 't2.value', 't1.value');

            $select = $adapter->select()
                ->from(
                    array('t1' => $attributeTable),
                    array('value' => $valueCheckSql))
                ->joinLeft(
                    array('t2' => $attributeTable),
                    't1.entity_id = t2.entity_id AND t1.attribute_id = t2.attribute_id AND t2.store_id = '
                        . (int)$storeId,
                    array('t1.entity_id')
                )
                ->where('t1.store_id = ?', \Magento\Core\Model\Store::DEFAULT_STORE_ID)
                ->where('t1.attribute_id = ?', $attribute->getAttributeId())
                ->where('t1.entity_id IN(?)', $productIds);
            $rows = $adapter->fetchPairs($select);
        }

        foreach ($productIds as $productId) {
            if (isset($rows[$productId])) {
                $statuses[$productId] = $rows[$productId];
            } else {
                $statuses[$productId] = -1;
            }
        }

        return $statuses;
    }
}
