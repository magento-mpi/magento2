<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Tag relation model
 *
 * @method Mage_Tag_Model_Resource_Tag_Relation _getResource()
 * @method Mage_Tag_Model_Resource_Tag_Relation getResource()
 * @method int getTagId()
 * @method Mage_Tag_Model_Tag_Relation setTagId(int $value)
 * @method int getCustomerId()
 * @method Mage_Tag_Model_Tag_Relation setCustomerId(int $value)
 * @method int getProductId()
 * @method Mage_Tag_Model_Tag_Relation setProductId(int $value)
 * @method int getStoreId()
 * @method Mage_Tag_Model_Tag_Relation setStoreId(int $value)
 * @method int getActive()
 * @method Mage_Tag_Model_Tag_Relation setActive(int $value)
 * @method string getCreatedAt()
 * @method Mage_Tag_Model_Tag_Relation setCreatedAt(string $value)
 *
 * @category    Mage
 * @package     Mage_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tag_Model_Tag_Relation extends Mage_Core_Model_Abstract
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
        $this->_init('Mage_Tag_Model_Resource_Tag_Relation');
    }

    /**
     * Retrieve Resource Instance wrapper
     *
     * @return Mage_Tag_Model_Resource_Tag_Relation
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Init indexing process after tag data save
     *
     * @return Mage_Tag_Model_Tag_Relation
     */
    protected function _afterSave()
    {
        parent::_afterSave();
        Mage::getSingleton('Mage_Index_Model_Indexer')->processEntityAction(
            $this, self::ENTITY, Mage_Index_Model_Event::TYPE_SAVE
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
     * @return Mage_Tag_Model_Tag_Relation
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
     * @return Mage_Tag_Model_Tag_Relation
     */
    public function deactivate()
    {
        $this->_getResource()->deactivate($this->getTagId(),  $this->getCustomerId());
        return $this;
    }

    /**
     * Add TAG to PRODUCT relations
     *
     * @param Mage_Tag_Model_Tag $model
     * @param array $productIds
     * @return Mage_Tag_Model_Tag_Relation
     */
    public function addRelations(Mage_Tag_Model_Tag $model, $productIds = array())
    {
        $this->setAddedProductIds($productIds);
        $this->setTagId($model->getTagId());
        $this->setCustomerId(null);
        $this->setStoreId($model->getStore());
        $this->_getResource()->addRelations($this);
        return $this;
    }
}
