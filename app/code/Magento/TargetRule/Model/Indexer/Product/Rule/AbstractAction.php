<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Model\Indexer\Product\Rule;

/**
 * Abstract action reindex class
 *
 * @package Magento\TargetRule\Model\Indexer\Product\Rule
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
     * Resource model instance
     *
     * @var \Magento\Framework\Model\Resource\Db\AbstractDb
     */
    protected $_resource;

    /**
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\TargetRule\Model\Resource\Rule\CollectionFactory $ruleFactory
     * @param \Magento\TargetRule\Model\Resource\Index $resource
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\TargetRule\Model\Resource\Rule\CollectionFactory $ruleFactory,
        \Magento\TargetRule\Model\Resource\Index $resource
    ) {
        $this->_productFactory = $productFactory;
        $this->_ruleCollectionFactory = $ruleFactory;
        $this->_resource = $resource;
    }

    /**
     * Execute action for given ids
     *
     * @param array|int $productIds
     *
     * @return void
     */
    abstract public function execute($productIds);

    /**
     * Refresh entities index
     *
     * @param array $productIds
     * @return array Affected ids
     */
    protected function _reindexRows($productIds = array())
    {
        foreach ($productIds as $productId) {
            $this->_reindex($productId);
        }
    }

    /**
     * Reindex all
     *
     * @return void
     */
    public function reindexAll()
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
     * Reindex targetrules
     *
     * @param int|null $productId
     * @return $this
     */
    protected function _reindex($productId = null)
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
