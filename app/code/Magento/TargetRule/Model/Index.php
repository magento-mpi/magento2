<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TargetRule Product Index by Rule Product List Type Model
 *
 * @method Magento_TargetRule_Model_Resource_Index getResource()
 * @method Magento_TargetRule_Model_Index setEntityId(int $value)
 * @method int getTypeId()
 * @method Magento_TargetRule_Model_Index setTypeId(int $value)
 * @method int getFlag()
 * @method Magento_TargetRule_Model_Index setFlag(int $value)
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_TargetRule_Model_Index extends Magento_Index_Model_Indexer_Abstract
{
    /**
     * Reindex products target-rules event type
     */
    const EVENT_TYPE_REINDEX_PRODUCTS = 'reindex_targetrules';

    /**
     * Clean target-rules event type
     */
    const EVENT_TYPE_CLEAN_TARGETRULES = 'clean_targetrule_index';

    /**
     * Product entity for indexers
     */
    const ENTITY_PRODUCT = 'targetrule_product';

    /**
     * Target-rule entity for indexers
     */
    const ENTITY_TARGETRULE = 'targetrule_entity';

    /**
     * Matched entities
     *
     * @var array
     */
    protected $_matchedEntities = array(
        self::ENTITY_PRODUCT => array(self::EVENT_TYPE_REINDEX_PRODUCTS),
        self::ENTITY_TARGETRULE => array(self::EVENT_TYPE_CLEAN_TARGETRULES)
    );

    /**
     * Whether the indexer should be displayed on process/list page
     *
     * @var bool
     */
    protected $_isVisible = false;

    /**
     * Target rule data
     *
     * @var Magento_TargetRule_Helper_Data
     */
    protected $_targetRuleData = null;

    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_session;

    /**
     * @var Magento_Index_Model_Indexer
     */
    protected $_indexer;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManger;

    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    protected $_ruleCollectionFactory;

    /**
     * @param Magento_TargetRule_Model_Resource_Rule_CollectionFactory $ruleFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Index_Model_Indexer $indexer
     * @param Magento_Customer_Model_Session $session
     * @param Magento_TargetRule_Helper_Data $targetRuleData
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_TargetRule_Model_Resource_Index $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_TargetRule_Model_Resource_Rule_CollectionFactory $ruleFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Index_Model_Indexer $indexer,
        Magento_Customer_Model_Session $session,
        Magento_TargetRule_Helper_Data $targetRuleData,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_TargetRule_Model_Resource_Index $resource,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_ruleCollectionFactory = $ruleFactory;
        $this->_storeManger = $storeManager;
        $this->_locale = $locale;
        $this->_indexer = $indexer;
        $this->_session = $session;
        $this->_targetRuleData = $targetRuleData;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_TargetRule_Model_Resource_Index');
    }

    /**
     * Retrieve resource instance
     *
     * @return Magento_TargetRule_Model_Resource_Index
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Set Catalog Product List identifier
     *
     * @param int $type
     * @return Magento_TargetRule_Model_Index
     */
    public function setType($type)
    {
        return $this->setData('type', $type);
    }

    /**
     * Retrieve Catalog Product List identifier
     *
     * @throws Magento_Core_Exception
     * @return int
     */
    public function getType()
    {
        $type = $this->getData('type');
        if (is_null($type)) {
            throw new Magento_Core_Exception(__('Undefined Catalog Product List Type'));
        }
        return $type;
    }

    /**
     * Set store scope
     *
     * @param int $storeId
     * @return Magento_TargetRule_Model_Index
     */
    public function setStoreId($storeId)
    {
        return $this->setData('store_id', $storeId);
    }

    /**
     * Retrieve store identifier scope
     *
     * @return int
     */
    public function getStoreId()
    {
        $storeId = $this->getData('store_id');
        if (is_null($storeId)) {
            $storeId = $this->_storeManger->getStore()->getId();
        }
        return $storeId;
    }

    /**
     * Set customer group identifier
     *
     * @param int $customerGroupId
     * @return Magento_TargetRule_Model_Index
     */
    public function setCustomerGroupId($customerGroupId)
    {
        return $this->setData('customer_group_id', $customerGroupId);
    }

    /**
     * Retrieve customer group identifier
     *
     * @return int
     */
    public function getCustomerGroupId()
    {
        $customerGroupId = $this->getData('customer_group_id');
        if (is_null($customerGroupId)) {
            $customerGroupId = $this->_session->getCustomerGroupId();
        }
        return $customerGroupId;
    }

    /**
     * Set result limit
     *
     * @param int $limit
     * @return Magento_TargetRule_Model_Index
     */
    public function setLimit($limit)
    {
        return $this->setData('limit', $limit);
    }

    /**
     * Retrieve result limit
     *
     * @return int
     */
    public function getLimit()
    {
        $limit = $this->getData('limit');
        if (is_null($limit)) {
            $limit = $this->_targetRuleData->getMaximumNumberOfProduct($this->getType());
        }
        return $limit;
    }

    /**
     * Set Product data object
     *
     * @param Magento_Object $product
     * @return Magento_TargetRule_Model_Index
     */
    public function setProduct(Magento_Object $product)
    {
        return $this->setData('product', $product);
    }

    /**
     * Retrieve Product data object
     *
     * @throws Magento_Core_Exception
     * @return Magento_Object
     */
    public function getProduct()
    {
        $product = $this->getData('product');
        if (!$product instanceof Magento_Object) {
            throw new Magento_Core_Exception(__('Please define a product data object.'));
        }
        return $product;
    }

    /**
     * Set product ids list be excluded
     *
     * @param int|array $productIds
     * @return Magento_TargetRule_Model_Index
     */
    public function setExcludeProductIds($productIds)
    {
        if (!is_array($productIds)) {
            $productIds = array($productIds);
        }
        return $this->setData('exclude_product_ids', $productIds);
    }

    /**
     * Retrieve Product Ids which must be excluded
     *
     * @return array
     */
    public function getExcludeProductIds()
    {
        $productIds = $this->getData('exclude_product_ids');
        if (!is_array($productIds)) {
            $productIds = array();
        }
        return $productIds;
    }

    /**
     * Retrieve related product Ids
     *
     * @return array
     */
    public function getProductIds()
    {
        return $this->_getResource()->getProductIds($this);
    }

    /**
     * Retrieve Rule collection by type and product
     *
     * @return Magento_TargetRule_Model_Resource_Rule_Collection
     */
    public function getRuleCollection()
    {
        /* @var $collection Magento_TargetRule_Model_Resource_Rule_Collection */
        $collection = $this->_ruleCollectionFactory->create();
        $collection->addApplyToFilter($this->getType())
            ->addProductFilter($this->getProduct()->getId())
            ->addIsActiveFilter()
            ->setPriorityOrder()
            ->setFlag('do_not_run_after_load', true);

        return $collection;
    }

    /**
     * Retrieve SELECT instance for conditions
     *
     * @return Magento_DB_Select
     */
    public function select()
    {
        return $this->_getResource()->select();
    }

    /**
     * Run processing by cron
     * Check store datetime and every day per store clean index cache
     *
     */
    public function cron()
    {
        $websites = $this->_storeManger->getWebsites();

        foreach ($websites as $website) {
            /* @var $website Magento_Core_Model_Website */
            $store = $website->getDefaultStore();
            $date  = $this->_locale->storeDate($store);
            if ($date->equals(0, Zend_Date::HOUR)) {
                $this->_indexer->logEvent(
                    new Magento_Object(array('type_id' => null, 'store' => $website->getStoreIds())),
                    self::ENTITY_TARGETRULE,
                    self::EVENT_TYPE_CLEAN_TARGETRULES
                );
            }
        }
        $this->_indexer->indexEvents(
            self::ENTITY_TARGETRULE,
            self::EVENT_TYPE_CLEAN_TARGETRULES
        );
    }

    /**
     * Get Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return __('Target Rules');
    }

    /**
     * Register indexer required data inside event object
     *
     * @param Magento_Index_Model_Event $event
     */
    protected function _registerEvent(Magento_Index_Model_Event $event)
    {
        switch ($event->getType()) {
            case self::EVENT_TYPE_REINDEX_PRODUCTS:
                switch ($event->getEntity()) {
                    case self::ENTITY_PRODUCT:
                        $event->addNewData('product', $event->getDataObject());
                        break;
                }
                break;
            case self::EVENT_TYPE_CLEAN_TARGETRULES:
                switch ($event->getEntity()) {
                    case self::ENTITY_TARGETRULE:
                        $event->addNewData('params', $event->getDataObject());
                        break;
                }
                break;
        }
    }

    /**
     * Process event based on event state data
     *
     * @param Magento_Index_Model_Event $event
     */
    protected function _processEvent(Magento_Index_Model_Event $event)
    {
        switch ($event->getType()) {
            case self::EVENT_TYPE_REINDEX_PRODUCTS:
                switch ($event->getEntity()) {
                    case self::ENTITY_PRODUCT:
                        $data = $event->getNewData();
                        if (!empty($data['product'])) {
                            $this->_reindex($data['product']);
                        }
                        break;
                }
                break;
            case self::EVENT_TYPE_CLEAN_TARGETRULES:
                switch ($event->getEntity()) {
                    case self::ENTITY_TARGETRULE:
                        $data = $event->getNewData();
                        if (!empty($data['params'])) {
                            $params = $data['params'];
                            $this->_cleanIndex($params->getTypeId(), $params->getStore());
                        }
                        break;
                }
                break;
        }
    }

    /**
     * Reindex targetrules
     *
     * @param Magento_Object $product
     * @return Magento_TargetRule_Model_Index
     */
    protected function _reindex($product)
    {
        $indexResource = $this->_getResource();

        // remove old cache index data
        $indexResource->removeIndexByProductIds($product->getId());

        // remove old matched product index
        $indexResource->removeProductIndex($product->getId());

        $ruleCollection = $this->_ruleCollectionFactory->create()
            ->addProductFilter($product->getId());

        foreach ($ruleCollection as $rule) {
            /** @var $rule Magento_TargetRule_Model_Rule */
            if ($rule->validate($product)) {
                $indexResource->saveProductIndex($rule->getId(), $product->getId(), $product->getStoreId());
            }
        }
        return $this;
    }

    /**
     * Remove targetrule's index
     *
     * @param int|null $typeId
     * @param Magento_Core_Model_Store|int|array|null $store
     * @return Magento_TargetRule_Model_Index
     */
    protected function _cleanIndex($typeId = null, $store = null)
    {
        $this->_getResource()->cleanIndex($typeId, $store);
        return $this;
    }
}
