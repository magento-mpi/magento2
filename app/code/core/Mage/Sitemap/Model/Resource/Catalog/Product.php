<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sitemap
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sitemap resource product collection model
 *
 * @category    Mage
 * @package     Mage_Sitemap
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sitemap_Model_Resource_Catalog_Product extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Collection Zend Db select
     *
     * @var Zend_Db_Select
     */
    protected $_select;

    /**
     * Attribute cache
     *
     * @var array
     */
    protected $_attributesCache    = array();

    /**
     * @var Mage_Catalog_Model_Product_Attribute_Backend_Media
     */
    protected $_mediaGalleryModel = null;

    /**
     * Init resource model (catalog/category)
     *
     */
    protected function _construct()
    {
        $this->_init('catalog_product_entity', 'entity_id');
    }

    /**
     * Add attribute to filter
     *
     * @param int $storeId
     * @param string $attributeCode
     * @param mixed $value
     * @param string $type
     * @return Zend_Db_Select|bool
     */
    protected function _addFilter($storeId, $attributeCode, $value, $type = '=')
    {
        if (!$this->_select instanceof Zend_Db_Select) {
            return false;
        }

        switch ($type) {
            case '=':
                $conditionRule = '=?';
                break;
            case 'in':
                $conditionRule = ' IN(?)';
                break;
            default:
                return false;
                break;
        }

        $attribute = $this->_getAttribute($attributeCode);
        if ($attribute['backend_type'] == 'static') {
            $this->_select->where('e.' . $attributeCode . $conditionRule, $value);
        } else {
            $this->_joinAttribute($storeId, $attributeCode);
            if ($attribute['is_global']) {
                $this->_select->where('t1_'.$attributeCode . '.value' . $conditionRule, $value);
            } else {
                $ifCase = $this->_select->getAdapter()->getCheckSql('t2_' . $attributeCode . '.value_id > 0',
                    't2_' . $attributeCode . '.value', 't1_' . $attributeCode . '.value');
                $this->_select->where('(' . $ifCase . ')' . $conditionRule, $value);
            }
        }

        return $this->_select;
    }

    /**
     * Join attribute by code
     *
     * @param int $storeId
     * @param string $attributeCode
     */
    protected function _joinAttribute($storeId, $attributeCode)
    {
        $attribute = $this->_getAttribute($attributeCode);
        $this->_select
            ->join(
                array('t1_' . $attributeCode => $attribute['table']),
                'e.entity_id = t1_' . $attributeCode . '.entity_id AND t1_' . $attributeCode . '.store_id = 0',
                array())
            ->where('t1_'.$attributeCode . '.attribute_id = ?', $attribute['attribute_id']);

        if (!$attribute['is_global']) {
            $this->_select->joinLeft(
                array('t2_' . $attributeCode => $attribute['table']),
                $this->_getWriteAdapter()->quoteInto('t1_' . $attributeCode . '.entity_id = t2_'
                    . $attributeCode . '.entity_id AND t1_' . $attributeCode . '.attribute_id = t2_'
                    . $attributeCode . '.attribute_id AND t2_' . $attributeCode . '.store_id=?',
                    $storeId),
                array()
            );
        }
    }

    /**
     * Get attribute data bu attribute code
     *
     * @param $attributeCode
     * @return array
     */
    protected function _getAttribute($attributeCode)
    {
        if (!isset($this->_attributesCache[$attributeCode])) {
            $attribute = Mage::getSingleton('Mage_Catalog_Model_Product')->getResource()->getAttribute($attributeCode);

            $this->_attributesCache[$attributeCode] = array(
                'entity_type_id' => $attribute->getEntityTypeId(),
                'attribute_id' => $attribute->getId(),
                'table' => $attribute->getBackend()->getTable(),
                'is_global' => $attribute->getIsGlobal() == Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                'backend_type' => $attribute->getBackendType()
            );
        }
        return $this->_attributesCache[$attributeCode];
    }

    /**
     * Get category collection array
     *
     * @param null|string|bool|int|Mage_Core_Model_Store $storeId
     * @return array
     */
    public function getCollection($storeId)
    {
        $products = array();

        /* @var $store Mage_Core_Model_Store */
        $store = Mage::app()->getStore($storeId);
        if (!$store) {
            return false;
        }

        $urConditions = array(
            'e.entity_id = ur.product_id',
            'ur.category_id IS NULL',
            $this->_getWriteAdapter()->quoteInto('ur.store_id = ?', $store->getId()),
            $this->_getWriteAdapter()->quoteInto('ur.is_system = ?', 1),
        );
        $this->_select = $this->_getWriteAdapter()->select()
            ->from(array('e' => $this->getMainTable()), array())
            ->join(
                array('w' => $this->getTable('catalog_product_website')),
                'e.entity_id = w.product_id',
                array()
            )
            ->joinLeft(
                array('ur' => $this->getTable('core_url_rewrite')),
                join(' AND ', $urConditions),
                array()
            );

        $this->_joinAttribute($storeId, 'name');
        $this->_joinAttribute($storeId, 'thumbnail');

        $this->_select->columns(array(
            'e.' . $this->getIdFieldName(),
            'e.updated_at',
            'url' => 'ur.request_path',
            'name' => new Zend_Db_Expr('IFNULL(t2_name.value, t1_name.value)'),
            'thumbnail' => new Zend_Db_Expr('IFNULL(t2_thumbnail.value, t1_thumbnail.value)')
        ));

        $this->_select->where('w.website_id = ?', $store->getWebsiteId());

        $this->_addFilter($storeId, 'visibility',
            Mage::getSingleton('Mage_Catalog_Model_Product_Visibility')->getVisibleInSiteIds(), 'in');
        $this->_addFilter($storeId, 'status',
            Mage::getSingleton('Mage_Catalog_Model_Product_Status')->getVisibleStatusIds(), 'in');

        $query = $this->_getWriteAdapter()->query($this->_select);
        while ($row = $query->fetch()) {
            $product = $this->_prepareProduct($row, $storeId);
            $products[$product->getId()] = $product;
        }

        return $products;
    }

    /**
     * Prepare product
     *
     * @param array $productRow
     * @param int $storeId
     * @return Varien_Object
     */
    protected function _prepareProduct(array $productRow, $storeId)
    {
        $product = new Varien_Object();

        $product->setId($productRow[$this->getIdFieldName()]);
        $productUrl = !empty($productRow['url']) ? $productRow['url'] : 'catalog/product/view/id/' . $product->getId();
        $product->setUrl($productUrl);
        $product->setUpdatedAt($productRow['updated_at']);
        $product->setName($productRow['name']);
        $product->setThumbnail($productRow['thumbnail']);

        // Load product images
        $product->setStoreId($storeId);
        $this->_loadProductImages($product);

        return $product;
    }

    /**
     * Load product images for sitemap
     *
     * @param Varien_Object $product
     */
    protected function _loadProductImages($product)
    {
        /** @var $mediaGallery Mage_Catalog_Model_Resource_Product_Attribute_Backend_Media */
        $mediaGallery = Mage::getResourceSingleton('Mage_Catalog_Model_Resource_Product_Attribute_Backend_Media');
        $gallery = $mediaGallery->loadGallery($product, $this->_getMediaGalleryModel());

        if ($gallery) {
            $imagesCollection = array();
            $productMediaPath = $this->_getMediaConfig()->getBaseMediaUrlAddition();
            foreach ($gallery as $image) {
                $imagesCollection[] = new Varien_Object(array(
                    'url' => $productMediaPath . $image['file'],
                    'caption' => $image['label'] ? $image['label'] : $image['label_default']
                ));
            }

            $thumbnail = $product->getThumbnail();
            if (!$thumbnail || $thumbnail == 'no_selection') {
                $thumbnail = $imagesCollection[0]->getUrl();
            }
            $product->setImages(new Varien_Object(array(
                'collection' => $imagesCollection,
                'title' => $product->getName(),
                'thumbnail' => $thumbnail
            )));
        }
    }

    /**
     * Get media gallery model
     *
     * @return Mage_Catalog_Model_Product_Attribute_Backend_Media|null
     */
    protected function _getMediaGalleryModel()
    {
        if (is_null($this->_mediaGalleryModel)) {
            /** @var $eavConfig Mage_Eav_Model_Config */
            $eavConfig = Mage::getModel('Mage_Eav_Model_Config');
            /** @var $eavConfig Mage_Eav_Model_Attribute */
            $mediaGallery = $eavConfig->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'media_gallery');
            $this->_mediaGalleryModel = $mediaGallery->getBackend();
        }
        return $this->_mediaGalleryModel;
    }

    /**
     * Get media config
     *
     * @return Mage_Catalog_Model_Product_Media_Config
     */
    protected function _getMediaConfig()
    {
        return Mage::getSingleton('Mage_Catalog_Model_Product_Media_Config');
    }
}
