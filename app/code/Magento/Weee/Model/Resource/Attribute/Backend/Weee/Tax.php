<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Weee
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Weee\Model\Resource\Attribute\Backend\Weee;

/**
 * Catalog product WEEE tax backend attribute model
 *
 * @category    Magento
 * @package     Magento_Weee
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Tax extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($resource);
    }

    /**
     * Defines main resource table and table identifier field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('weee_tax', 'value_id');
    }

    /**
     * Load product data
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute
     * @return array
     */
    public function loadProductData($product, $attribute)
    {
        $select = $this->_getReadAdapter()->select()->from(
            $this->getMainTable(),
            array('website_id', 'country', 'state', 'value')
        )->where(
            'entity_id = ?',
            (int)$product->getId()
        )->where(
            'attribute_id = ?',
            (int)$attribute->getId()
        );
        if ($attribute->isScopeGlobal()) {
            $select->where('website_id = ?', 0);
        } else {
            $storeId = $product->getStoreId();
            if ($storeId) {
                $select->where(
                    'website_id IN (?)',
                    array(0, $this->_storeManager->getStore($storeId)->getWebsiteId())
                );
            }
        }
        return $this->_getReadAdapter()->fetchAll($select);
    }

    /**
     * Delete product data
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute
     * @return $this
     */
    public function deleteProductData($product, $attribute)
    {
        $where = array('entity_id = ?' => (int)$product->getId(), 'attribute_id = ?' => (int)$attribute->getId());

        $adapter = $this->_getWriteAdapter();
        if (!$attribute->isScopeGlobal()) {
            $storeId = $product->getStoreId();
            if ($storeId) {
                $where['website_id IN(?)'] = array(0, $this->_storeManager->getStore($storeId)->getWebsiteId());
            }
        }
        $adapter->delete($this->getMainTable(), $where);
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
        $data['entity_id'] = (int)$product->getId();
        $data['entity_type_id'] = (int)$product->getEntityTypeId();

        $this->_getWriteAdapter()->insert($this->getMainTable(), $data);
        return $this;
    }
}
