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
 * @method \Magento\CatalogInventory\Model\Resource\Indexer\Stock getResource()
 * @method int getProductId()
 * @method \Magento\CatalogInventory\Model\Indexer\Stock setProductId(int $value)
 * @method int getWebsiteId()
 * @method \Magento\CatalogInventory\Model\Indexer\Stock setWebsiteId(int $value)
 * @method int getStockId()
 * @method \Magento\CatalogInventory\Model\Indexer\Stock setStockId(int $value)
 * @method float getQty()
 * @method \Magento\CatalogInventory\Model\Indexer\Stock setQty(float $value)
 * @method int getStockStatus()
 * @method \Magento\CatalogInventory\Model\Indexer\Stock setStockStatus(int $value)
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogInventory\Model\Indexer;

class Stock extends \Magento\Index\Model\Indexer\AbstractIndexer
{
    /**
     * Data key for matching result to be saved in
     */
    const EVENT_MATCH_RESULT_KEY = 'cataloginventory_stock_match_result';

    /**
     * @var array
     */
    protected $_matchedEntities = array(
        \Magento\CatalogInventory\Model\Stock\Item::ENTITY => array(
            \Magento\Index\Model\Event::TYPE_SAVE
        ),
        \Magento\Catalog\Model\Product::ENTITY => array(
            \Magento\Index\Model\Event::TYPE_SAVE,
            \Magento\Index\Model\Event::TYPE_MASS_ACTION,
            \Magento\Index\Model\Event::TYPE_DELETE
        ),
        \Magento\Core\Model\Store::ENTITY => array(
            \Magento\Index\Model\Event::TYPE_SAVE
        ),
        \Magento\Core\Model\Store\Group::ENTITY => array(
            \Magento\Index\Model\Event::TYPE_SAVE
        ),
        \Magento\Core\Model\Config\Value::ENTITY => array(
            \Magento\Index\Model\Event::TYPE_SAVE
        ),
    );

    /**
     * Related config settings
     *
     * @var array
     */
    protected $_relatedConfigSettings = array(
        \Magento\CatalogInventory\Model\Stock\Item::XML_PATH_MANAGE_STOCK,
        \Magento\CatalogInventory\Helper\Data::XML_PATH_SHOW_OUT_OF_STOCK
    );

    /**
     * Catalog inventory data
     *
     * @var \Magento\CatalogInventory\Helper\Data
     */
    protected $_catalogInventoryData = null;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_CatalogInventory_Helper_Data $catalogInventoryData
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_CatalogInventory_Helper_Data $catalogInventoryData,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_catalogInventoryData = $catalogInventoryData;
    }

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\CatalogInventory\Model\Resource\Indexer\Stock');
    }

    /**
     * Retrieve resource instance wrapper
     *
     * @return \Magento\CatalogInventory\Model\Resource\Indexer\Stock
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
     * @param \Magento\Index\Model\Event $event
     * @return bool
     */
    public function matchEvent(\Magento\Index\Model\Event $event)
    {
        $data       = $event->getNewData();
        if (isset($data[self::EVENT_MATCH_RESULT_KEY])) {
            return $data[self::EVENT_MATCH_RESULT_KEY];
        }

        $entity = $event->getEntity();
        if ($entity == \Magento\Core\Model\Store::ENTITY) {
            /* @var $store \Magento\Core\Model\Store */
            $store = $event->getDataObject();
            if ($store && $store->isObjectNew()) {
                $result = true;
            } else {
                $result = false;
            }
        } else if ($entity == \Magento\Core\Model\Store\Group::ENTITY) {
            /* @var $storeGroup \Magento\Core\Model\Store\Group */
            $storeGroup = $event->getDataObject();
            if ($storeGroup && $storeGroup->dataHasChangedFor('website_id')) {
                $result = true;
            } else {
                $result = false;
            }
        } else if ($entity == \Magento\Core\Model\Config\Value::ENTITY) {
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
     * @param \Magento\Index\Model\Event $event
     */
    protected function _registerEvent(\Magento\Index\Model\Event $event)
    {
        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, true);
        switch ($event->getEntity()) {
            case \Magento\CatalogInventory\Model\Stock\Item::ENTITY:
                $this->_registerCatalogInventoryStockItemEvent($event);
                break;

            case \Magento\Catalog\Model\Product::ENTITY:
                $this->_registerCatalogProductEvent($event);
                break;

            case \Magento\Core\Model\Store::ENTITY:
            case \Magento\Core\Model\Store\Group::ENTITY:
            case \Magento\Core\Model\Config\Value::ENTITY:
                $event->addNewData('cataloginventory_stock_skip_call_event_handler', true);
                $process = $event->getProcess();
                $process->changeStatus(\Magento\Index\Model\Process::STATUS_REQUIRE_REINDEX);

                if ($event->getEntity() == \Magento\Core\Model\Config\Value::ENTITY) {
                    $configData = $event->getDataObject();
                    if ($configData->getPath() == \Magento\CatalogInventory\Helper\Data::XML_PATH_SHOW_OUT_OF_STOCK) {
                        \Mage::getSingleton('Magento\Index\Model\Indexer')->getProcessByCode('catalog_product_price')
                            ->changeStatus(\Magento\Index\Model\Process::STATUS_REQUIRE_REINDEX);
                        \Mage::getSingleton('Magento\Index\Model\Indexer')->getProcessByCode('catalog_product_attribute')
                            ->changeStatus(\Magento\Index\Model\Process::STATUS_REQUIRE_REINDEX);
                    }
                }
                break;
        }
    }

    /**
     * Register data required by catalog product processes in event object
     *
     * @param \Magento\Index\Model\Event $event
     */
    protected function _registerCatalogProductEvent(\Magento\Index\Model\Event $event)
    {
        switch ($event->getType()) {
            case \Magento\Index\Model\Event::TYPE_SAVE:
                $product = $event->getDataObject();
                if ($product && $product->getStockData()) {
                    $product->setForceReindexRequired(true);
                }
                break;
            case \Magento\Index\Model\Event::TYPE_MASS_ACTION:
                $this->_registerCatalogProductMassActionEvent($event);
                break;

            case \Magento\Index\Model\Event::TYPE_DELETE:
                $this->_registerCatalogProductDeleteEvent($event);
                break;
        }
    }

    /**
     * Register data required by cataloginventory stock item processes in event object
     *
     * @param \Magento\Index\Model\Event $event
     */
    protected function _registerCatalogInventoryStockItemEvent(\Magento\Index\Model\Event $event)
    {
        switch ($event->getType()) {
            case \Magento\Index\Model\Event::TYPE_SAVE:
                $this->_registerStockItemSaveEvent($event);
                break;
        }
    }

    /**
     * Register data required by stock item save process in event object
     *
     * @param \Magento\Index\Model\Event $event
     * @return \Magento\CatalogInventory\Model\Indexer\Stock
     */
    protected function _registerStockItemSaveEvent(\Magento\Index\Model\Event $event)
    {
        /* @var $object \Magento\CatalogInventory\Model\Stock\Item */
        $object      = $event->getDataObject();

        $event->addNewData('reindex_stock', 1);
        $event->addNewData('product_id', $object->getProductId());

        // Saving stock item without product object
        // Register re-index price process if products out of stock hidden on Front-end
        if (!$this->_catalogInventoryData->isShowOutOfStock() && !$object->getProduct()) {
            $massObject = new \Magento\Object();
            $massObject->setAttributesData(array('force_reindex_required' => 1));
            $massObject->setProductIds(array($object->getProductId()));
            \Mage::getSingleton('Magento\Index\Model\Indexer')->logEvent(
                $massObject, \Magento\Catalog\Model\Product::ENTITY, \Magento\Index\Model\Event::TYPE_MASS_ACTION
            );
        }

        return $this;
    }

    /**
     * Register data required by product delete process in event object
     *
     * @param \Magento\Index\Model\Event $event
     * @return \Magento\CatalogInventory\Model\Indexer\Stock
     */
    protected function _registerCatalogProductDeleteEvent(\Magento\Index\Model\Event $event)
    {
        /* @var $product \Magento\Catalog\Model\Product */
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
     * @param \Magento\Index\Model\Event $event
     * @return \Magento\CatalogInventory\Model\Indexer\Stock
     */
    protected function _registerCatalogProductMassActionEvent(\Magento\Index\Model\Event $event)
    {
        /* @var $actionObject \Magento\Object */
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
     * @param \Magento\Index\Model\Event $event
     */
    protected function _processEvent(\Magento\Index\Model\Event $event)
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
