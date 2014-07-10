<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
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
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

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
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\TargetRule\Model\RuleFactory $ruleFactory
     * @param \Magento\TargetRule\Model\Resource\Rule\CollectionFactory $ruleCollectionFactory
     * @param \Magento\TargetRule\Model\Resource\Index $resource
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\TargetRule\Model\RuleFactory $ruleFactory,
        \Magento\TargetRule\Model\Resource\Rule\CollectionFactory $ruleCollectionFactory,
        \Magento\TargetRule\Model\Resource\Index $resource
    ) {
        $this->_productFactory = $productFactory;
        $this->_ruleFactory = $ruleFactory;
        $this->_resource = $resource;
        $this->_ruleCollectionFactory = $ruleCollectionFactory;
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
    protected function _reindexByProductIds($productIds = array())
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
        $indexResource->removeProductIndex(array());

        $ruleCollection = $this->_ruleCollectionFactory->create();

        foreach ($ruleCollection as $rule) {
            $indexResource->saveProductIndex($rule);
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

        $product = $this->_productFactory->create()->load($productId);
        foreach ($ruleCollection as $rule) {
            /** @var $rule \Magento\TargetRule\Model\Rule */
            if ($rule->validate($product)) {
                $indexResource->saveProductIndex($rule);
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
    protected function _reindexByRuleIds($ruleIds = array())
    {
        foreach ($ruleIds as $ruleId) {
            $this->_reindexByRuleId($ruleId);
        }
    }

    /**
     * Reindex rule by ID
     *
     * @param int $ruleId
     */
    protected function _reindexByRuleId($ruleId)
    {
        $this->_resource->saveProductIndex($this->_ruleFactory->create()->load($ruleId));
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
        $this->_resource->cleanIndex($typeId, $store);
        return $this;
    }

}
