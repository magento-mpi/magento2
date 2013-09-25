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
 * Catalog Product Eav Indexer Model
 *
 * @method Magento_Catalog_Model_Resource_Product_Indexer_Eav _getResource()
 * @method Magento_Catalog_Model_Resource_Product_Indexer_Eav getResource()
 * @method Magento_Catalog_Model_Product_Indexer_Eav setEntityId(int $value)
 * @method int getAttributeId()
 * @method Magento_Catalog_Model_Product_Indexer_Eav setAttributeId(int $value)
 * @method int getStoreId()
 * @method Magento_Catalog_Model_Product_Indexer_Eav setStoreId(int $value)
 * @method int getValue()
 * @method Magento_Catalog_Model_Product_Indexer_Eav setValue(int $value)
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Indexer_Eav extends Magento_Index_Model_Indexer_Abstract
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
        Magento_Catalog_Model_Resource_Eav_Attribute::ENTITY => array(
            Magento_Index_Model_Event::TYPE_SAVE,
        ),
    );

    /**
     * Eav config
     *
     * @var Magento_Eav_Model_Config
     */
    protected $_eavConfig;

    /**
     * Construct
     *
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Eav_Model_Config $eavConfig,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_eavConfig = $eavConfig;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return __('Product Attributes');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return __('Index product attributes for layered navigation building');
    }

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Catalog_Model_Resource_Product_Indexer_Eav');
    }

    /**
     * Register data required by process in event object
     *
     * @param Magento_Index_Model_Event $event
     */
    protected function _registerEvent(Magento_Index_Model_Event $event)
    {
        $entity = $event->getEntity();

        if ($entity == Magento_Catalog_Model_Product::ENTITY) {
            switch ($event->getType()) {
                case Magento_Index_Model_Event::TYPE_DELETE:
                    $this->_registerCatalogProductDeleteEvent($event);
                    break;

                case Magento_Index_Model_Event::TYPE_SAVE:
                    $this->_registerCatalogProductSaveEvent($event);
                    break;

                case Magento_Index_Model_Event::TYPE_MASS_ACTION:
                    $this->_registerCatalogProductMassActionEvent($event);
                    break;
            }
        } else if ($entity == Magento_Catalog_Model_Resource_Eav_Attribute::ENTITY) {
            switch ($event->getType()) {
                case Magento_Index_Model_Event::TYPE_SAVE:
                    $this->_registerCatalogAttributeSaveEvent($event);
                    break;
            }
        }
    }

    /**
     * Check is attribute indexable in EAV
     *
     * @param Magento_Catalog_Model_Resource_Eav_Attribute|string $attribute
     * @return bool
     */
    protected function _attributeIsIndexable($attribute)
    {
        if (!$attribute instanceof Magento_Catalog_Model_Resource_Eav_Attribute) {
            $attribute = $this->_eavConfig
                ->getAttribute(Magento_Catalog_Model_Product::ENTITY, $attribute);
        }

        return $attribute->isIndexable();
    }

    /**
     * Register data required by process in event object
     *
     * @param Magento_Index_Model_Event $event
     * @return Magento_Catalog_Model_Product_Indexer_Eav
     */
    protected function _registerCatalogProductSaveEvent(Magento_Index_Model_Event $event)
    {
        /* @var $product Magento_Catalog_Model_Product */
        $product    = $event->getDataObject();
        $attributes = $product->getAttributes();
        $reindexEav = $product->getForceReindexRequired();
        foreach ($attributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            if ($this->_attributeIsIndexable($attribute) && $product->dataHasChangedFor($attributeCode)) {
                $reindexEav = true;
                break;
            }
        }

        if ($reindexEav) {
            $event->addNewData('reindex_eav', $reindexEav);
        }

        return $this;
    }

    /**
     * Register data required by process in event object
     *
     * @param Magento_Index_Model_Event $event
     * @return Magento_Catalog_Model_Product_Indexer_Eav
     */
    protected function _registerCatalogProductDeleteEvent(Magento_Index_Model_Event $event)
    {
        /* @var $product Magento_Catalog_Model_Product */
        $product    = $event->getDataObject();

        $parentIds  = $this->_getResource()->getRelationsByChild($product->getId());
        if ($parentIds) {
            $event->addNewData('reindex_eav_parent_ids', $parentIds);
        }

        return $this;
    }

    /**
     * Register data required by process in event object
     *
     * @param Magento_Index_Model_Event $event
     * @return Magento_Catalog_Model_Product_Indexer_Eav
     */
    protected function _registerCatalogProductMassActionEvent(Magento_Index_Model_Event $event)
    {
        $reindexEav = false;

        /* @var $actionObject Magento_Object */
        $actionObject = $event->getDataObject();
        // check if attributes changed
        $attrData = $actionObject->getAttributesData();
        if (is_array($attrData)) {
            foreach (array_keys($attrData) as $attributeCode) {
                if ($this->_attributeIsIndexable($attributeCode)) {
                    $reindexEav = true;
                    break;
                }
            }
        }

        // check changed websites
        if ($actionObject->getWebsiteIds()) {
            $reindexEav = true;
        }

        // register affected products
        if ($reindexEav) {
            $event->addNewData('reindex_eav_product_ids', $actionObject->getProductIds());
        }

        return $this;
    }

    /**
     * Register data required by process attribute save in event object
     *
     * @param Magento_Index_Model_Event $event
     * @return Magento_Catalog_Model_Product_Indexer_Eav
     */
    protected function _registerCatalogAttributeSaveEvent(Magento_Index_Model_Event $event)
    {
        /* @var $attribute Magento_Catalog_Model_Resource_Eav_Attribute */
        $attribute = $event->getDataObject();
        if ($attribute->isIndexable()) {
            $before = $attribute->getOrigData('is_filterable')
                || $attribute->getOrigData('is_filterable_in_search')
                || $attribute->getOrigData('is_visible_in_advanced_search');
            $after  = $attribute->getData('is_filterable')
                || $attribute->getData('is_filterable_in_search')
                || $attribute->getData('is_visible_in_advanced_search');

            if (!$before && $after || $before && !$after) {
                $event->addNewData('reindex_attribute', 1);
                $event->addNewData('attribute_index_type', $attribute->getIndexType());
                $event->addNewData('is_indexable', $after);
            }
        }

        return $this;
    }

    /**
     * Process event
     *
     * @param Magento_Index_Model_Event $event
     */
    protected function _processEvent(Magento_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (!empty($data['catalog_product_eav_reindex_all'])) {
            $this->reindexAll();
        }
        if (empty($data['catalog_product_eav_skip_call_event_handler'])) {
            $this->callEventHandler($event);
        }
    }
}
