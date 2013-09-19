<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CatalogInventory Stock Status Indexer Model
 *
 * @method Magento_CatalogInventory_Model_Resource_Indexer_Stock getResource()
 * @method int getProductId()
 * @method Magento_CatalogInventory_Model_Indexer_Stock setProductId(int $value)
 * @method int getWebsiteId()
 * @method Magento_CatalogInventory_Model_Indexer_Stock setWebsiteId(int $value)
 * @method int getStockId()
 * @method Magento_CatalogInventory_Model_Indexer_Stock setStockId(int $value)
 * @method float getQty()
 * @method Magento_CatalogInventory_Model_Indexer_Stock setQty(float $value)
 * @method int getStockStatus()
 * @method Magento_CatalogInventory_Model_Indexer_Stock setStockStatus(int $value)
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogInventory_Model_Indexer_Stock extends Magento_Index_Model_Indexer_Abstract
{
    /**
     * Data key for matching result to be saved in
     */
    const EVENT_MATCH_RESULT_KEY = 'cataloginventory_stock_match_result';

    /**
     * @var array
     */
    protected $_matchedEntities = array(
        Magento_CatalogInventory_Model_Stock_Item::ENTITY => array(
            Magento_Index_Model_Event::TYPE_SAVE
        ),
        Magento_Catalog_Model_Product::ENTITY => array(
            Magento_Index_Model_Event::TYPE_SAVE,
            Magento_Index_Model_Event::TYPE_MASS_ACTION,
            Magento_Index_Model_Event::TYPE_DELETE
        ),
        Magento_Core_Model_Store::ENTITY => array(
            Magento_Index_Model_Event::TYPE_SAVE
        ),
        Magento_Core_Model_Store_Group::ENTITY => array(
            Magento_Index_Model_Event::TYPE_SAVE
        ),
        Magento_Core_Model_Config_Value::ENTITY => array(
            Magento_Index_Model_Event::TYPE_SAVE
        ),
    );

    /**
     * Related config settings
     *
     * @var array
     */
    protected $_relatedConfigSettings = array(
        Magento_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK,
        Magento_CatalogInventory_Helper_Data::XML_PATH_SHOW_OUT_OF_STOCK
    );

    /**
     * Catalog inventory data
     *
     * @var Magento_CatalogInventory_Helper_Data
     */
    protected $_catalogInventoryData = null;

    /**
     * @var Magento_Index_Model_Indexer
     */
    protected $_indexer;

    /**
     * @param Magento_Index_Model_Indexer $indexer
     * @param Magento_CatalogInventory_Helper_Data $catalogInventoryData
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Index_Model_Indexer $indexer,
        Magento_CatalogInventory_Helper_Data $catalogInventoryData,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_indexer = $indexer;
        $this->_catalogInventoryData = $catalogInventoryData;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_CatalogInventory_Model_Resource_Indexer_Stock');
    }

    /**
     * Retrieve resource instance wrapper
     *
     * @return Magento_CatalogInventory_Model_Resource_Indexer_Stock
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return __('Stock Status');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return __('Index Product Stock Status');
    }

    /**
     * Check if event can be matched by process.
     * Overwrote for specific config save, store and store groups save matching
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

        $entity = $event->getEntity();
        if ($entity == Magento_Core_Model_Store::ENTITY) {
            /* @var $store Magento_Core_Model_Store */
            $store = $event->getDataObject();
            if ($store && $store->isObjectNew()) {
                $result = true;
            } else {
                $result = false;
            }
        } else if ($entity == Magento_Core_Model_Store_Group::ENTITY) {
            /* @var $storeGroup Magento_Core_Model_Store_Group */
            $storeGroup = $event->getDataObject();
            if ($storeGroup && $storeGroup->dataHasChangedFor('website_id')) {
                $result = true;
            } else {
                $result = false;
            }
        } else if ($entity == Magento_Core_Model_Config_Value::ENTITY) {
            $configData = $event->getDataObject();
            if ($configData && in_array($configData->getPath(), $this->_relatedConfigSettings)) {
                $result = $configData->isValueChanged();
            } else {
                $result = false;
            }
        } else {
            $result = parent::matchEvent($event);
        }

        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, $result);

        return $result;
    }

    /**
     * Register data required by process in event object
     *
     * @param Magento_Index_Model_Event $event
     */
    protected function _registerEvent(Magento_Index_Model_Event $event)
    {
        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, true);
        switch ($event->getEntity()) {
            case Magento_CatalogInventory_Model_Stock_Item::ENTITY:
                $this->_registerCatalogInventoryStockItemEvent($event);
                break;

            case Magento_Catalog_Model_Product::ENTITY:
                $this->_registerCatalogProductEvent($event);
                break;

            case Magento_Core_Model_Store::ENTITY:
            case Magento_Core_Model_Store_Group::ENTITY:
            case Magento_Core_Model_Config_Value::ENTITY:
                $event->addNewData('cataloginventory_stock_skip_call_event_handler', true);
                $process = $event->getProcess();
                $process->changeStatus(Magento_Index_Model_Process::STATUS_REQUIRE_REINDEX);

                if ($event->getEntity() == Magento_Core_Model_Config_Value::ENTITY) {
                    $configData = $event->getDataObject();
                    if ($configData->getPath() == Magento_CatalogInventory_Helper_Data::XML_PATH_SHOW_OUT_OF_STOCK) {
                        $this->_indexer->getProcessByCode('catalog_product_price')
                            ->changeStatus(Magento_Index_Model_Process::STATUS_REQUIRE_REINDEX);
                        $this->_indexer->getProcessByCode('catalog_product_attribute')
                            ->changeStatus(Magento_Index_Model_Process::STATUS_REQUIRE_REINDEX);
                    }
                }
                break;
        }
    }

    /**
     * Register data required by catalog product processes in event object
     *
     * @param Magento_Index_Model_Event $event
     */
    protected function _registerCatalogProductEvent(Magento_Index_Model_Event $event)
    {
        switch ($event->getType()) {
            case Magento_Index_Model_Event::TYPE_SAVE:
                $product = $event->getDataObject();
                if ($product && $product->getStockData()) {
                    $product->setForceReindexRequired(true);
                }
                break;
            case Magento_Index_Model_Event::TYPE_MASS_ACTION:
                $this->_registerCatalogProductMassActionEvent($event);
                break;

            case Magento_Index_Model_Event::TYPE_DELETE:
                $this->_registerCatalogProductDeleteEvent($event);
                break;
        }
    }

    /**
     * Register data required by cataloginventory stock item processes in event object
     *
     * @param Magento_Index_Model_Event $event
     */
    protected function _registerCatalogInventoryStockItemEvent(Magento_Index_Model_Event $event)
    {
        switch ($event->getType()) {
            case Magento_Index_Model_Event::TYPE_SAVE:
                $this->_registerStockItemSaveEvent($event);
                break;
        }
    }

    /**
     * Register data required by stock item save process in event object
     *
     * @param Magento_Index_Model_Event $event
     * @return Magento_CatalogInventory_Model_Indexer_Stock
     */
    protected function _registerStockItemSaveEvent(Magento_Index_Model_Event $event)
    {
        /* @var $object Magento_CatalogInventory_Model_Stock_Item */
        $object      = $event->getDataObject();

        $event->addNewData('reindex_stock', 1);
        $event->addNewData('product_id', $object->getProductId());

        // Saving stock item without product object
        // Register re-index price process if products out of stock hidden on Front-end
        if (!$this->_catalogInventoryData->isShowOutOfStock() && !$object->getProduct()) {
            $massObject = new Magento_Object();
            $massObject->setAttributesData(array('force_reindex_required' => 1));
            $massObject->setProductIds(array($object->getProductId()));
            $this->_indexer->logEvent(
                $massObject, Magento_Catalog_Model_Product::ENTITY, Magento_Index_Model_Event::TYPE_MASS_ACTION
            );
        }

        return $this;
    }

    /**
     * Register data required by product delete process in event object
     *
     * @param Magento_Index_Model_Event $event
     * @return Magento_CatalogInventory_Model_Indexer_Stock
     */
    protected function _registerCatalogProductDeleteEvent(Magento_Index_Model_Event $event)
    {
        /* @var $product Magento_Catalog_Model_Product */
        $product = $event->getDataObject();

        $parentIds = $this->_getResource()->getProductParentsByChild($product->getId());
        if ($parentIds) {
            $event->addNewData('reindex_stock_parent_ids', $parentIds);
        }

        return $this;
    }

    /**
     * Register data required by product mass action process in event object
     *
     * @param Magento_Index_Model_Event $event
     * @return Magento_CatalogInventory_Model_Indexer_Stock
     */
    protected function _registerCatalogProductMassActionEvent(Magento_Index_Model_Event $event)
    {
        /* @var $actionObject Magento_Object */
        $actionObject = $event->getDataObject();
        $attributes   = array(
            'status'
        );
        $reindexStock = false;

        // check if attributes changed
        $attrData = $actionObject->getAttributesData();
        if (is_array($attrData)) {
            foreach ($attributes as $attributeCode) {
                if (array_key_exists($attributeCode, $attrData)) {
                    $reindexStock = true;
                    break;
                }
            }
        }

        // check changed websites
        if ($actionObject->getWebsiteIds()) {
            $reindexStock = true;
        }

        // register affected products
        if ($reindexStock) {
            $event->addNewData('reindex_stock_product_ids', $actionObject->getProductIds());
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
        if (!empty($data['cataloginventory_stock_reindex_all'])) {
            $this->reindexAll();
        }
        if (empty($data['cataloginventory_stock_skip_call_event_handler'])) {
            $this->callEventHandler($event);
        }
    }
}
