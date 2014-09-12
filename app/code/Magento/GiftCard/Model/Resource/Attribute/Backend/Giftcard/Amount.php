<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCard\Model\Resource\Attribute\Backend\Giftcard;

/**
 * Giftcard Amount Backend Model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Amount extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Store manager
     *
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource,
        \Magento\Framework\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($resource);
    }

    /**
     * Define main table and primary key
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_giftcard_amount', 'value_id');
    }

    /**
     * Load product data by product and attribute_id
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     * @return array
     */
    public function loadProductData($product, $attribute)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()->from(
            $this->getMainTable(),
            array('website_id', 'value')
        )->where(
            'entity_id=:product_id'
        )->where(
            'attribute_id=:attribute_id'
        );
        $bind = array('product_id' => $product->getId(), 'attribute_id' => $attribute->getId());
        if ($attribute->isScopeGlobal()) {
            $select->where('website_id=0');
        } else {
            if ($storeId = $product->getStoreId()) {
                $select->where('website_id IN (0, :website_id)');
                $bind['website_id'] = $this->_storeManager->getStore($storeId)->getWebsiteId();
            }
        }
        return $read->fetchAll($select, $bind);
    }

    /**
     * Delete product data
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     * @return $this
     */
    public function deleteProductData($product, $attribute)
    {
        $condition = array();

        if (!$attribute->isScopeGlobal()) {
            if ($storeId = $product->getStoreId()) {
                $condition['website_id IN (?)'] = array(0, $this->_storeManager->getStore($storeId)->getWebsiteId());
            }
        }

        $condition['entity_id=?'] = $product->getId();
        $condition['attribute_id=?'] = $attribute->getId();

        $this->_getWriteAdapter()->delete($this->getMainTable(), $condition);
        return $this;
    }

    /**
     * Insert product data
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $data
     * @return $this
     */
    public function insertProductData($product, $data)
    {
        $data['entity_id'] = $product->getId();
        $data['entity_type_id'] = $product->getEntityTypeId();

        $this->_getWriteAdapter()->insert($this->getMainTable(), $data);
        return $this;
    }
}
