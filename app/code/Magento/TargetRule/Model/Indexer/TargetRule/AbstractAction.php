<?php
/**
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\TargetRule\Model\Indexer\TargetRule;

/**
 * Abstract action reindex class
 *
 * @package Magento\TargetRule\Model\Indexer\TargetRule
 */
abstract class AbstractAction
{
    /**
     * @var \Magento\TargetRule\Model\Resource\Rule\CollectionFactory
     */
    protected $_ruleCollectionFactory;

    /**
     * @var \Magento\TargetRule\Model\RuleFactory
     */
    protected $_ruleFactory;

    /**
     * Resource model instance
     *
     * @var \Magento\Framework\Model\Resource\Db\AbstractDb
     */
    protected $_resource;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var array
     */
    protected $_isIndexCleaned = [];

    /**
     * @param \Magento\TargetRule\Model\RuleFactory $ruleFactory
     * @param \Magento\TargetRule\Model\Resource\Rule\CollectionFactory $ruleCollectionFactory
     * @param \Magento\TargetRule\Model\Resource\Index $resource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     */
    public function __construct(
        \Magento\TargetRule\Model\RuleFactory $ruleFactory,
        \Magento\TargetRule\Model\Resource\Rule\CollectionFactory $ruleCollectionFactory,
        \Magento\TargetRule\Model\Resource\Index $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    ) {
        $this->_ruleFactory = $ruleFactory;
        $this->_resource = $resource;
        $this->_ruleCollectionFactory = $ruleCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_localeDate = $localeDate;
    }
    /**
     * Execute action for given ids
     *
     * @param array|int $ids
     *
     * @return void
     */
    abstract public function execute($ids);

    /**
     * Refresh entities index
     *
     * @param array $productIds
     * @return array Affected ids
     */
    protected function _reindexByProductIds($productIds = [])
    {
        foreach ($productIds as $productId) {
            $this->_reindexByProductId($productId);
        }
    }

    /**
     * Reindex all
     *
     * @return void
     */
    protected function _reindexAll()
    {
        $indexResource = $this->_resource;

        // remove old cache index data
        $this->_cleanIndex();
        $indexResource->removeProductIndex([]);

        $ruleCollection = $this->_ruleCollectionFactory->create();

        foreach ($ruleCollection as $rule) {
            $indexResource->saveProductIndex($rule);
        }
    }

    /**
     * Clean all
     *
     * @return void
     */
    protected function _cleanAll()
    {
        $websites = $this->_storeManager->getWebsites();
        foreach ($websites as $website) {
            /* @var $website \Magento\Store\Model\Website */
            $store = $website->getDefaultStore();
            $date = $this->_localeDate->scopeDate($store);
            if ($date->equals(0, \Zend_Date::HOUR)) {
                $storeIds = $website->getStoreIds();
                $this->_cleanIndex(null, $storeIds);
            }
        }
    }

    /**
     * Reindex targetrules by product id
     *
     * @param int|null $productId
     * @return $this
     */
    protected function _reindexByProductId($productId = null)
    {
        $indexResource = $this->_resource;

        // remove old cache index data
        $this->_cleanIndex();

        // remove old matched product index
        $indexResource->removeProductIndex($productId);

        $ruleCollection = $this->_ruleCollectionFactory->create();

        foreach ($ruleCollection as $rule) {
            /** @var $rule \Magento\TargetRule\Model\Rule */
            if ($rule->validateByEntityId($productId)) {
                $matchedProductIds = $rule->getMatchingProductIds();
                $rule->getResource()->bindRuleToEntity($rule->getId(), $matchedProductIds, 'product');
                $rule->getResource()->cleanCachedDataByProductIds(
                    array_unique(
                        array_merge(
                            [$productId],
                            $matchedProductIds
                        )
                    )
                );
            }
        }
        return $this;
    }

    /**
     * Refresh entities index by rule Ids
     *
     * @param array $ruleIds
     * @return array Affected ids
     */
    protected function _reindexByRuleIds($ruleIds = [])
    {
        foreach ($ruleIds as $ruleId) {
            $this->_reindexByRuleId($ruleId);
        }
    }

    /**
     * Reindex rule by ID
     *
     * @param int $ruleId
     * @return void
     */
    protected function _reindexByRuleId($ruleId)
    {
        /** @var \Magento\TargetRule\Model\Rule $rule */
        $rule = $this->_ruleFactory->create();
        $rule->load($ruleId);
        // remove old cache index data
        $this->_cleanIndex();
        /** @var \Magento\TargetRule\Model\Resource\Rule $ruleResource */
        $ruleResource = $rule->getResource();
        $productIdsBeforeUnbind = $ruleResource->getAssociatedEntityIds($ruleId, 'product');
        $ruleResource->unbindRuleFromEntity($ruleId, [], 'product');
        if ($rule->getId()) {
            $matchedProductIds = $rule->getMatchingProductIds();
        } else {
            $matchedProductIds = [];
        }
        $ruleResource->bindRuleToEntity($ruleId, $matchedProductIds, 'product');
        $ruleResource->cleanCachedDataByProductIds(
            array_unique(
                array_merge(
                    $productIdsBeforeUnbind,
                    $matchedProductIds
                )
            )
        );
    }

    /**
     * Remove targetrule's index
     *
     * @param int|null $typeId
     * @param \Magento\Store\Model\Store|int|array|null $store
     * @return $this
     */
    protected function _cleanIndex($typeId = null, $store = null)
    {
        if (!$this->_isIndexCleaned($typeId, $store)) {
            $this->_resource->cleanIndex($typeId, $store);
        }
        return $this;
    }

    /**
     * Remove products from index
     *
     * @param int|null $productId
     * @return $this
     */
    protected function _deleteProductFromIndex($productId = null)
    {
        $this->_resource->deleteProductFromIndex($productId);

        return $this;
    }

    /**
     * Is index cleaned
     *
     * @param null|int $typeId
     * @param null|int $store
     * @return bool
     */
    protected function _isIndexCleaned($typeId = null, $store = null)
    {
        return isset($this->_isIndexCleaned[$typeId][$store]) ?  $this->_isIndexCleaned[$typeId][$store] : false;
    }

    /**
     * Set index cleaned flag
     *
     * @param null|int $typeId
     * @param null|int $store
     * @param bool $flag
     * @return $this
     */
    protected function _setIndexCleaned($typeId = null, $store = null, $flag = true)
    {
        $this->_isIndexCleaned[$typeId][$store] = $flag;
        return $this;
    }
}
