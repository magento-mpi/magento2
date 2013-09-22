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
 * @method \Magento\TargetRule\Model\Resource\Index getResource()
 * @method \Magento\TargetRule\Model\Index setEntityId(int $value)
 * @method int getTypeId()
 * @method \Magento\TargetRule\Model\Index setTypeId(int $value)
 * @method int getFlag()
 * @method \Magento\TargetRule\Model\Index setFlag(int $value)
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.LongVariable)
 */
namespace Magento\TargetRule\Model;

class Index extends \Magento\Index\Model\Indexer\AbstractIndexer
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
     * @var \Magento\TargetRule\Helper\Data
     */
    protected $_targetRuleData = null;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Index\Model\Indexer
     */
    protected $_indexer;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManger;

    /**
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

    protected $_ruleCollectionFactory;

    /**
     * @param \Magento\TargetRule\Model\Resource\Rule\CollectionFactory $ruleCollectionFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Index\Model\Indexer $indexer
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\TargetRule\Helper\Data $targetRuleData
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\TargetRule\Model\Resource\Index $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\TargetRule\Model\Resource\Rule\CollectionFactory $ruleCollectionFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Index\Model\Indexer $indexer,
        \Magento\Customer\Model\Session $session,
        \Magento\TargetRule\Helper\Data $targetRuleData,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\TargetRule\Model\Resource\Index $resource,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_ruleCollectionFactory = $ruleCollectionFactory;
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
        $this->_init('Magento\TargetRule\Model\Resource\Index');
    }

    /**
     * Retrieve resource instance
     *
     * @return \Magento\TargetRule\Model\Resource\Index
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Set Catalog Product List identifier
     *
     * @param int $type
     * @return \Magento\TargetRule\Model\Index
     */
    public function setType($type)
    {
        return $this->setData('type', $type);
    }

    /**
     * Retrieve Catalog Product List identifier
     *
     * @throws \Magento\Core\Exception
     * @return int
     */
    public function getType()
    {
        $type = $this->getData('type');
        if (is_null($type)) {
            throw new \Magento\Core\Exception(__('Undefined Catalog Product List Type'));
        }
        return $type;
    }

    /**
     * Set store scope
     *
     * @param int $storeId
     * @return \Magento\TargetRule\Model\Index
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
     * @return \Magento\TargetRule\Model\Index
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
     * @return \Magento\TargetRule\Model\Index
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
     * @param \Magento\Object $product
     * @return \Magento\TargetRule\Model\Index
     */
    public function setProduct(\Magento\Object $product)
    {
        return $this->setData('product', $product);
    }

    /**
     * Retrieve Product data object
     *
     * @throws \Magento\Core\Exception
     * @return \Magento\Object
     */
    public function getProduct()
    {
        $product = $this->getData('product');
        if (!$product instanceof \Magento\Object) {
            throw new \Magento\Core\Exception(__('Please define a product data object.'));
        }
        return $product;
    }

    /**
     * Set product ids list be excluded
     *
     * @param int|array $productIds
     * @return \Magento\TargetRule\Model\Index
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
     * @return \Magento\TargetRule\Model\Resource\Rule\Collection
     */
    public function getRuleCollection()
    {
        /* @var $collection \Magento\TargetRule\Model\Resource\Rule\Collection */
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
     * @return \Magento\DB\Select
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
            /* @var $website \Magento\Core\Model\Website */
            $store = $website->getDefaultStore();
            $date  = $this->_locale->storeDate($store);
            if ($date->equals(0, \Zend_Date::HOUR)) {
                $this->_indexer->logEvent(
                    new \Magento\Object(array('type_id' => null, 'store' => $website->getStoreIds())),
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
     * @param \Magento\Index\Model\Event $event
     */
    protected function _registerEvent(\Magento\Index\Model\Event $event)
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
     * @param \Magento\Index\Model\Event $event
     */
    protected function _processEvent(\Magento\Index\Model\Event $event)
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
     * @param \Magento\Object $product
     * @return \Magento\TargetRule\Model\Index
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
            /** @var $rule \Magento\TargetRule\Model\Rule */
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
     * @param \Magento\Core\Model\Store|int|array|null $store
     * @return \Magento\TargetRule\Model\Index
     */
    protected function _cleanIndex($typeId = null, $store = null)
    {
        $this->_getResource()->cleanIndex($typeId, $store);
        return $this;
    }
}
