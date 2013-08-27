<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Tag relation model
 *
 * @method Magento_Tag_Model_Resource_Tag_Relation _getResource()
 * @method Magento_Tag_Model_Resource_Tag_Relation getResource()
 * @method int getTagId()
 * @method Magento_Tag_Model_Tag_Relation setTagId(int $value)
 * @method int getCustomerId()
 * @method Magento_Tag_Model_Tag_Relation setCustomerId(int $value)
 * @method int getProductId()
 * @method Magento_Tag_Model_Tag_Relation setProductId(int $value)
 * @method int getStoreId()
 * @method Magento_Tag_Model_Tag_Relation setStoreId(int $value)
 * @method int getActive()
 * @method Magento_Tag_Model_Tag_Relation setActive(int $value)
 * @method string getCreatedAt()
 * @method Magento_Tag_Model_Tag_Relation setCreatedAt(string $value)
 *
 * @category    Magento
 * @package     Magento_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Model_Tag_Relation extends Magento_Core_Model_Abstract
{
    /**
     * Relation statuses
     */
    const STATUS_ACTIVE     = 1;
    const STATUS_NOT_ACTIVE = 0;

    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY = 'tag_relation';

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Tag_Model_Resource_Tag_Relation');
    }

    /**
     * Retrieve Resource Instance wrapper
     *
     * @return Magento_Tag_Model_Resource_Tag_Relation
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Init indexing process after tag data save
     *
     * @return Magento_Tag_Model_Tag_Relation
     */
    protected function _afterSave()
    {
        parent::_afterSave();
        Mage::getSingleton('Magento_Index_Model_Indexer')->processEntityAction(
            $this, self::ENTITY, Magento_Index_Model_Event::TYPE_SAVE
        );
        return $this;
    }

    /**
     * Load relation by Product (optional), tag, customer and store
     *
     * @param int $productId
     * @param int $tagId
     * @param int $customerId
     * @param int $storeId
     * @return Magento_Tag_Model_Tag_Relation
     */
    public function loadByTagCustomer($productId=null, $tagId, $customerId, $storeId=null)
    {
        $this->setProductId($productId);
        $this->setTagId($tagId);
        $this->setCustomerId($customerId);
        if(!is_null($storeId)) {
            $this->setStoreId($storeId);
        }
        $this->_getResource()->loadByTagCustomer($this);
        return $this;
    }

    /**
     * Retrieve Relation Product Ids
     *
     * @return array
     */
    public function getProductIds()
    {
        $ids = $this->getData('product_ids');
        if (is_null($ids)) {
            $ids = $this->_getResource()->getProductIds($this);
            $this->setProductIds($ids);
        }
        return $ids;
    }

    /**
     * Retrieve list of related tag ids for products specified in current object
     *
     * @return array
     */
    public function getRelatedTagIds()
    {
        if (is_null($this->getData('related_tag_ids'))) {
            $this->setRelatedTagIds($this->_getResource()->getRelatedTagIds($this));
        }
        return $this->getData('related_tag_ids');
    }

    /**
     * Deactivate tag relations (using current settings)
     *
     * @return Magento_Tag_Model_Tag_Relation
     */
    public function deactivate()
    {
        $this->_getResource()->deactivate($this->getTagId(),  $this->getCustomerId());
        return $this;
    }

    /**
     * Add TAG to PRODUCT relations
     *
     * @param Magento_Tag_Model_Tag $model
     * @param array $productIds
     * @return Magento_Tag_Model_Tag_Relation
     */
    public function addRelations(Magento_Tag_Model_Tag $model, $productIds = array())
    {
        $this->setAddedProductIds($productIds);
        $this->setTagId($model->getTagId());
        $this->setCustomerId(null);
        $this->setStoreId($model->getStore());
        $this->_getResource()->addRelations($this);
        return $this;
    }
}
