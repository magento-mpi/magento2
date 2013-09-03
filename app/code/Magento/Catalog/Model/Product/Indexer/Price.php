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
 * @method Magento_Catalog_Model_Resource_Product_Indexer_Price _getResource()
 * @method Magento_Catalog_Model_Resource_Product_Indexer_Price getResource()
 * @method Magento_Catalog_Model_Product_Indexer_Price setEntityId(int $value)
 * @method int getCustomerGroupId()
 * @method Magento_Catalog_Model_Product_Indexer_Price setCustomerGroupId(int $value)
 * @method int getWebsiteId()
 * @method Magento_Catalog_Model_Product_Indexer_Price setWebsiteId(int $value)
 * @method int getTaxClassId()
 * @method Magento_Catalog_Model_Product_Indexer_Price setTaxClassId(int $value)
 * @method float getPrice()
 * @method Magento_Catalog_Model_Product_Indexer_Price setPrice(float $value)
 * @method float getFinalPrice()
 * @method Magento_Catalog_Model_Product_Indexer_Price setFinalPrice(float $value)
 * @method float getMinPrice()
 * @method Magento_Catalog_Model_Product_Indexer_Price setMinPrice(float $value)
 * @method float getMaxPrice()
 * @method Magento_Catalog_Model_Product_Indexer_Price setMaxPrice(float $value)
 * @method float getTierPrice()
 * @method Magento_Catalog_Model_Product_Indexer_Price setTierPrice(float $value)
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Indexer_Price extends Magento_Index_Model_Indexer_Abstract
{
    /**
     * Data key for matching result to be saved in
     */
    const EVENT_MATCH_RESULT_KEY = 'catalog_product_price_match_result';

    /**
     * Reindex price event type
     */
    const EVENT_TYPE_REINDEX_PRICE = 'catalog_reindex_price';

    /**
     * Matched Entities instruction array
     *
     * @var array
     */
    protected $_matchedEntities = array(
        Magento_Catalog_Model_Product::ENTITY => array(
            Magento_Index_Model_Event::TYPE_SAVE,
            Magento_Index_Model_Event::TYPE_DELETE,
            Magento_Index_Model_Event::TYPE_MASS_ACTION,
            self::EVENT_TYPE_REINDEX_PRICE,
        ),
        Magento_Core_Model_Config_Data::ENTITY => array(
            Magento_Index_Model_Event::TYPE_SAVE
        ),
        Magento_Customer_Model_Group::ENTITY => array(
            Magento_Index_Model_Event::TYPE_SAVE
        )
    );

    protected $_relatedConfigSettings = array(
        Magento_Catalog_Helper_Data::XML_PATH_PRICE_SCOPE,
        Magento_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK
    );

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Catalog_Model_Resource_Product_Indexer_Price');
    }

    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return __('Product Prices');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return __('Index product prices');
    }

    /**
     * Retrieve attribute list has an effect on product price
     *
     * @return array
     */
    protected function _getDependentAttributes()
    {
        return array(
            'price',
            'special_price',
            'special_from_date',
            'special_to_date',
            'tax_class_id',
            'status',
            'required_options',
            'force_reindex_required'
        );
    }

    /**
     * Check if event can be matched by process.
     * Rewrited for checking configuration settings save (like price scope).
     *
     * @param Magento_Index_Model_Event $event
     * @return bool
     */
    public function matchEvent(Magento_Index_Model_Event $event)
    {
        $data       = $event->getNewData();
        if (isset($data[self::EVENT_MATCH_RESULT_KEY])) {
            return $data[self::EVENT_MATCH_RESULT_KEY];
        }

        if ($event->getEntity() == Magento_Core_Model_Config_Data::ENTITY) {
            $data = $event->getDataObject();
            if ($data && in_array($data->getPath(), $this->_relatedConfigSettings)) {
                $result = $data->isValueChanged();
            } else {
                $result = false;
            }
        } elseif ($event->getEntity() == Magento_Customer_Model_Group::ENTITY) {
            $result = $event->getDataObject() && $event->getDataObject()->isObjectNew();
        } else {
            $result = parent::matchEvent($event);
        }

        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, $result);

        return $result;
    }

    /**
     * Register data required by catalog product delete process
     *
     * @param Magento_Index_Model_Event $event
     */
    protected function _registerCatalogProductDeleteEvent(Magento_Index_Model_Event $event)
    {
        /* @var $product Magento_Catalog_Model_Product */
        $product = $event->getDataObject();

        $parentIds = $this->_getResource()->getProductParentsByChild($product->getId());
        if ($parentIds) {
            $event->addNewData('reindex_price_parent_ids', $parentIds);
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
        $product      = $event->getDataObject();
        $attributes   = $this->_getDependentAttributes();
        $reindexPrice = $product->getIsRelationsChanged() || $product->getIsCustomOptionChanged()
            || $product->dataHasChangedFor('tier_price_changed')
            || $product->getIsChangedWebsites()
            || $product->getForceReindexRequired();

        foreach ($attributes as $attributeCode) {
            $reindexPrice = $reindexPrice || $product->dataHasChangedFor($attributeCode);
        }

        if ($reindexPrice) {
            $event->addNewData('product_type_id', $product->getTypeId());
            $event->addNewData('reindex_price', 1);
        }
    }

    /**
     * @param Magento_Index_Model_Event $event
     */
    protected function _registerCatalogProductMassActionEvent(Magento_Index_Model_Event $event)
    {
        /* @var $actionObject \Magento\Object */
        $actionObject = $event->getDataObject();
        $attributes   = $this->_getDependentAttributes();
        $reindexPrice = false;

        // check if attributes changed
        $attrData = $actionObject->getAttributesData();
        if (is_array($attrData)) {
            foreach ($attributes as $attributeCode) {
                if (array_key_exists($attributeCode, $attrData)) {
                    $reindexPrice = true;
                    break;
                }
            }
        }

        // check changed websites
        if ($actionObject->getWebsiteIds()) {
            $reindexPrice = true;
        }

        // register affected products
        if ($reindexPrice) {
            $event->addNewData('reindex_price_product_ids', $actionObject->getProductIds());
        }
    }

    /**
     * Register data required by process in event object
     *
     * @param Magento_Index_Model_Event $event
     */
    protected function _registerEvent(Magento_Index_Model_Event $event)
    {
        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, true);
        $entity = $event->getEntity();

        if ($entity == Magento_Core_Model_Config_Data::ENTITY || $entity == Magento_Customer_Model_Group::ENTITY) {
            $process = $event->getProcess();
            $process->changeStatus(Magento_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        } else if ($entity == Magento_Catalog_Model_Product::ENTITY) {
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
                case self::EVENT_TYPE_REINDEX_PRICE:
                    $event->addNewData('id', $event->getDataObject()->getId());
                    break;
                default:
                    break;
            }

            // call product type indexers registerEvent
            $indexers = $this->_getResource()->getTypeIndexers();
            foreach ($indexers as $indexer) {
                $indexer->registerEvent($event);
            }
        }
    }

    /**
     * Process event
     *
     * @param Magento_Index_Model_Event $event
     */
    protected function _processEvent(Magento_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if ($event->getType() == self::EVENT_TYPE_REINDEX_PRICE) {
            $this->_getResource()->reindexProductIds($data['id']);
            return;
        }
        if (!empty($data['catalog_product_price_reindex_all'])) {
            $this->reindexAll();
        }
        if (empty($data['catalog_product_price_skip_call_event_handler'])) {
            $this->callEventHandler($event);
        }
    }
}
