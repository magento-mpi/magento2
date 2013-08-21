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
 * Tag Indexer Model
 *
 * @method Magento_Tag_Model_Resource_Indexer_Summary _getResource()
 * @method Magento_Tag_Model_Resource_Indexer_Summary getResource()
 * @method int getTagId()
 * @method Magento_Tag_Model_Indexer_Summary setTagId(int $value)
 * @method int getStoreId()
 * @method Magento_Tag_Model_Indexer_Summary setStoreId(int $value)
 * @method int getCustomers()
 * @method Magento_Tag_Model_Indexer_Summary setCustomers(int $value)
 * @method int getProducts()
 * @method Magento_Tag_Model_Indexer_Summary setProducts(int $value)
 * @method int getUses()
 * @method Magento_Tag_Model_Indexer_Summary setUses(int $value)
 * @method int getHistoricalUses()
 * @method Magento_Tag_Model_Indexer_Summary setHistoricalUses(int $value)
 * @method int getPopularity()
 * @method Magento_Tag_Model_Indexer_Summary setPopularity(int $value)
 * @method int getBasePopularity()
 * @method Magento_Tag_Model_Indexer_Summary setBasePopularity(int $value)
 *
 * @category    Magento
 * @package     Magento_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Model_Indexer_Summary extends Magento_Index_Model_Indexer_Abstract
{
    /**
     * @var array
     */
    protected $_matchedEntities = array(
        Magento_Catalog_Model_Product::ENTITY => array(
            Magento_Index_Model_Event::TYPE_SAVE,
            Magento_Index_Model_Event::TYPE_DELETE,
            Magento_Index_Model_Event::TYPE_MASS_ACTION,
        ),
        Magento_Tag_Model_Tag::ENTITY => array(
            Magento_Index_Model_Event::TYPE_SAVE
        ),
        Magento_Tag_Model_Tag_Relation::ENTITY => array(
            Magento_Index_Model_Event::TYPE_SAVE
        )
    );

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Tag_Model_Resource_Indexer_Summary');
    }

    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return __('Tag Aggregation Data');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return __('Rebuild Tag aggregation data');
    }

    /**
     * Retrieve attribute list that has an effect on tags
     *
     * @return array
     */
    protected function _getProductAttributesDependOn()
    {
        return array(
            'visibility',
            'status',
            'website_ids'
        );
    }

    /**
     * Register data required by process in event object
     *
     * @param Magento_Index_Model_Event $event
     */
    protected function _registerEvent(Magento_Index_Model_Event $event)
    {
        if ($event->getEntity() == Magento_Catalog_Model_Product::ENTITY) {
            $this->_registerCatalogProduct($event);
        } elseif ($event->getEntity() == Magento_Tag_Model_Tag::ENTITY) {
            $this->_registerTag($event);
        } elseif ($event->getEntity() == Magento_Tag_Model_Tag_Relation::ENTITY) {
            $this->_registerTagRelation($event);
        }
    }

    /**
     * Register data required by catalog product save process
     *
     * @param Magento_Index_Model_Event $event
     */
    protected function _registerCatalogProductSaveEvent(Magento_Index_Model_Event $event)
    {
        /* @var $product Magento_Catalog_Model_Product */
        $product = $event->getDataObject();
        $reindexTag = $product->getForceReindexRequired();

        foreach ($this->_getProductAttributesDependOn() as $attributeCode) {
            $reindexTag = $reindexTag || $product->dataHasChangedFor($attributeCode);
        }

        if (!$product->isObjectNew() && $reindexTag) {
            $event->addNewData('tag_reindex_required', true);
        }
    }

    /**
     * Register data required by catalog product delete process
     *
     * @param Magento_Index_Model_Event $event
     */
    protected function _registerCatalogProductDeleteEvent(Magento_Index_Model_Event $event)
    {
        $tagIds = Mage::getModel('Magento_Tag_Model_Tag_Relation')
            ->setProductId($event->getEntityPk())
            ->getRelatedTagIds();
        if ($tagIds) {
            $event->addNewData('tag_reindex_tag_ids', $tagIds);
        }
    }

    /**
     * Register data required by catalog product massaction process
     *
     * @param Magento_Index_Model_Event $event
     */
    protected function _registerCatalogProductMassActionEvent(Magento_Index_Model_Event $event)
    {
        /* @var $actionObject Magento_Object */
        $actionObject = $event->getDataObject();
        $attributes   = $this->_getProductAttributesDependOn();
        $reindexTags  = false;

        // check if attributes changed
        $attrData = $actionObject->getAttributesData();
        if (is_array($attrData)) {
            foreach ($attributes as $attributeCode) {
                if (array_key_exists($attributeCode, $attrData)) {
                    $reindexTags = true;
                    break;
                }
            }
        }

        // check changed websites
        if ($actionObject->getWebsiteIds()) {
            $reindexTags = true;
        }

        // register affected tags
        if ($reindexTags) {
            $tagIds = Mage::getModel('Magento_Tag_Model_Tag_Relation')
                ->setProductId($actionObject->getProductIds())
                ->getRelatedTagIds();
            if ($tagIds) {
                $event->addNewData('tag_reindex_tag_ids', $tagIds);
            }
        }
    }

    protected function _registerCatalogProduct(Magento_Index_Model_Event $event)
    {
        switch ($event->getType()) {
            case Magento_Index_Model_Event::TYPE_SAVE:
                $this->_registerCatalogProductSaveEvent($event);
                break;

            case Magento_Index_Model_Event::TYPE_DELETE:
                $this->_registerCatalogProductDeleteEvent($event);
                break;

            case Magento_Index_Model_Event::TYPE_MASS_ACTION:
                $this->_registerCatalogProductMassActionEvent($event);
                break;
        }
    }

    protected function _registerTag(Magento_Index_Model_Event $event)
    {
        if ($event->getType() == Magento_Index_Model_Event::TYPE_SAVE) {
            $event->addNewData('tag_reindex_tag_id', $event->getEntityPk());
        }
    }

    protected function _registerTagRelation(Magento_Index_Model_Event $event)
    {
        if ($event->getType() == Magento_Index_Model_Event::TYPE_SAVE) {
            $event->addNewData('tag_reindex_tag_id', $event->getDataObject()->getTagId());
        }
    }

    /**
     * Process event
     *
     * @param Magento_Index_Model_Event $event
     */
    protected function _processEvent(Magento_Index_Model_Event $event)
    {
        $this->callEventHandler($event);
    }
}
