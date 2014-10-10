<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogRule\Model\Indexer\Product;

use Magento\CatalogRule\Model\Indexer\Product\ProductProcessor;
use Magento\CatalogRule\Model\Resource\Rule\CollectionFactory as RuleCollectionFactory;
use Magento\CatalogRule\Model\Rule;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Catalog\Model\Product;

class ProductRuleIndexerBuilder
{
    /**
     * Store number of seconds in a day
     */
    const SECONDS_IN_DAY = 86400;

    /**
     * @var RuleCollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Framework\App\Resource
     */
    protected $resource;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Rule\Model\Condition\Sql\Builder
     */
    protected $sqlBuilder;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @param RuleCollectionFactory $ruleCollectionFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Framework\Stdlib\DateTime $dateFormat
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(
        RuleCollectionFactory $ruleCollectionFactory,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Framework\Logger $logger,
        \Magento\Framework\Stdlib\DateTime $dateFormat,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\App\Resource $resource,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->eventManager = $eventManager;
        $this->eavConfig = $eavConfig;
        $this->priceCurrency = $priceCurrency;
        $this->storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->resource = $resource;
        $this->sqlBuilder = $sqlBuilder;
        $this->productFactory = $productFactory;
    }

    /**
     * Reindex by id
     *
     * @param int $id
     * @return void
     */
    public function reindexById($id)
    {
        $this->reindexByIds([$id]);
    }

    /**
     * Reindex by ids
     *
     * @param array $ids
     * @return void
     */
    public function reindexByIds(array $ids)
    {
        $this->cleanByIds($ids);
        $rules = $this->ruleCollectionFactory->create()
            ->addFieldToFilter('is_active', 1);

        $collectedData = [];
        foreach ($rules as $rule) {
            foreach ($ids as $productId) {
                if (empty($collectedData[$productId])) {
                    $collectedData[$productId] = $this->productFactory->create()->load($productId);
                }
                $this->applyToProduct($rule, $collectedData[$productId]);
            }
        }
    }

    public function reindexFull()
    {

    }

    /**
     * Clean by product ids
     *
     * @return void
     */
    public function cleanByIds($productIds)
    {
        $this->getWriteAdapter()->deleteFromSelect(
            $this->getWriteAdapter()->select(
                $this->resource->getTableName('catalogrule_product'),
                'product_id'
            )->distinct()
            ->where(
                'product_id IN (?)',
                $productIds
            ),
            $this->resource->getTableName('catalogrule_product')
        );

        $this->getWriteAdapter()->deleteFromSelect(
            $this->getWriteAdapter()->select(
                $this->resource->getTableName('catalogrule_product_price'),
                'product_id'
            )->distinct()
                ->where(
                    'product_id IN (?)',
                    $productIds
                ),
            $this->resource->getTableName('catalogrule_product_price')
        );
    }


    /**
     * Retrieve connection for read data
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected function getReadAdapter()
    {
        $writeAdapter = $this->getWriteAdapter();
        if ($writeAdapter && $writeAdapter->getTransactionLevel() > 0) {
            // if transaction is started we should use write connection for reading
            return $writeAdapter;
        }
        return $this->resource->getConnection('read');
    }

    /**
     * Retrieve connection for write data
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected function getWriteAdapter()
    {
        return $this->resource->getConnection('write');
    }

    protected function updateRuleProductData(Rule $rule)
    {
        $ruleId = $rule->getId();
        $write = $this->getWriteAdapter();
        $cleanCondition = $write->quoteInto('rule_id=?', $ruleId);
        if ($rule->getProductsFilter()) {
            $cleanCondition = array('rule_id=?' => $ruleId, 'product_id IN (?)' => $rule->getProductsFilter());
        }
        $write->delete(
            $this->resource->getTableName('catalogrule_product'),
            $cleanCondition
        );

        if (!$rule->getIsActive()) {
            return $this;
        }

        $websiteIds = $rule->getWebsiteIds();
        if (!is_array($websiteIds)) {
            $websiteIds = explode(',', $websiteIds);
        }
        if (empty($websiteIds)) {
            return $this;
        }

        $productIds = $this->getMatchingProductIdsByRule($rule);

        $customerGroupIds = $rule->getCustomerGroupIds();
        $fromTime = strtotime($rule->getFromDate());
        $toTime = strtotime($rule->getToDate());
        $toTime = $toTime ? $toTime + self::SECONDS_IN_DAY - 1 : 0;
        $sortOrder = (int)$rule->getSortOrder();
        $actionOperator = $rule->getSimpleAction();
        $actionAmount = $rule->getDiscountAmount();
        $subActionOperator = $rule->getSubIsEnable() ? $rule->getSubSimpleAction() : '';
        $subActionAmount = $rule->getSubDiscountAmount();
        $actionStop = $rule->getStopRulesProcessing();

        $rows = array();

        try {
            foreach ($productIds as $productId => $validationByWebsite) {
                foreach ($websiteIds as $websiteId) {
                    if (empty($validationByWebsite[$websiteId])) {
                        continue;
                    }
                    foreach ($customerGroupIds as $customerGroupId) {
                        $rows[] = array(
                            'rule_id' => $ruleId,
                            'from_time' => $fromTime,
                            'to_time' => $toTime,
                            'website_id' => $websiteId,
                            'customer_group_id' => $customerGroupId,
                            'product_id' => $productId,
                            'action_operator' => $actionOperator,
                            'action_amount' => $actionAmount,
                            'action_stop' => $actionStop,
                            'sort_order' => $sortOrder,
                            'sub_simple_action' => $subActionOperator,
                            'sub_discount_amount' => $subActionAmount
                        );

                        if (count($rows) == 1000) {
                            $write->insertMultiple($this->resource->getTableName('catalogrule_product'), $rows);
                            $rows = array();
                        }
                    }
                }
            }
            if (!empty($rows)) {
                $write->insertMultiple($this->resource->getTableName('catalogrule_product'), $rows);
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return $this;
    }

    protected function applyAllRulesForDateRange($productId = null)
    {
        $write = $this->getWriteAdapter();

        $fromDate = mktime(0, 0, 0, date('m'), date('d') - 1);
        $toDate = mktime(0, 0, 0, date('m'), date('d') + 1);

        $product = null;
        if ($productId instanceof Product) {
            $product = $productId;
            $productId = $productId->getId();
        }

        $dayPrices = array();

        try {
            /**
             * Update products rules prices per each website separately
             * because of max join limit in mysql
             */
            foreach ($this->storeManager->getWebsites(false) as $website) {
                $productsStmt = $this->getRuleProductsStmt($fromDate, $toDate, $productId, $website->getId());

                $dayPrices = array();
                $stopFlags = array();
                $prevKey = null;

                while ($ruleData = $productsStmt->fetch()) {
                    $ruleProductId = $ruleData['product_id'];
                    $productKey = $ruleProductId .
                        '_' .
                        $ruleData['website_id'] .
                        '_' .
                        $ruleData['customer_group_id'];

                    if ($prevKey && $prevKey != $productKey) {
                        $stopFlags = array();
                    }

                    /**
                     * Build prices for each day
                     */
                    for ($time = $fromDate; $time <= $toDate; $time += self::SECONDS_IN_DAY) {
                        if (($ruleData['from_time'] == 0 ||
                                $time >= $ruleData['from_time']) && ($ruleData['to_time'] == 0 ||
                                $time <= $ruleData['to_time'])
                        ) {
                            $priceKey = $time . '_' . $productKey;

                            if (isset($stopFlags[$priceKey])) {
                                continue;
                            }

                            if (!isset($dayPrices[$priceKey])) {
                                $dayPrices[$priceKey] = array(
                                    'rule_date' => $time,
                                    'website_id' => $ruleData['website_id'],
                                    'customer_group_id' => $ruleData['customer_group_id'],
                                    'product_id' => $ruleProductId,
                                    'rule_price' => $this->calcRuleProductPrice($ruleData),
                                    'latest_start_date' => $ruleData['from_time'],
                                    'earliest_end_date' => $ruleData['to_time']
                                );
                            } else {
                                $dayPrices[$priceKey]['rule_price'] = $this->calcRuleProductPrice(
                                    $ruleData,
                                    $dayPrices[$priceKey]
                                );
                                $dayPrices[$priceKey]['latest_start_date'] = max(
                                    $dayPrices[$priceKey]['latest_start_date'],
                                    $ruleData['from_time']
                                );
                                $dayPrices[$priceKey]['earliest_end_date'] = min(
                                    $dayPrices[$priceKey]['earliest_end_date'],
                                    $ruleData['to_time']
                                );
                            }

                            if ($ruleData['action_stop']) {
                                $stopFlags[$priceKey] = true;
                            }
                        }
                    }

                    $prevKey = $productKey;
                    if (count($dayPrices) > 1000) {
                        $this->saveRuleProductPrices($dayPrices);
                        $dayPrices = array();
                    }
                }
                $this->saveRuleProductPrices($dayPrices);
            }
            $this->saveRuleProductPrices($dayPrices);

            $write->delete($this->resource->getTableName('catalogrule_group_website'), array());

            $timestamp = $this->dateTime->gmtTimestamp();

            $select = $write->select()->distinct(
                true
            )->from(
                $this->resource->getTableName('catalogrule_product'),
                array('rule_id', 'customer_group_id', 'website_id')
            )->where(
                "{$timestamp} >= from_time AND (({$timestamp} <= to_time AND to_time > 0) OR to_time = 0)"
            );
            $query = $select->insertFromSelect($this->resource->getTableName('catalogrule_group_website'));
            $write->query($query);
        } catch (\Exception $e) {
            $this->logger->logException($e);
            throw $e;
        }
        //TODO
//        $productCondition = $this->_conditionFactory->create()->setTable(
//            $this->resource->getTableName('catalogrule_affected_product')
//        )->setPkFieldName(
//            'product_id'
//        );
//        $this->eventManager->dispatch(
//            'catalogrule_after_apply',
//            array('product' => $product, 'product_condition' => $productCondition)
//        );
//        $write->delete($this->resource->getTableName('catalogrule_affected_product'));

        return $this;
    }

    protected function saveRuleProductPrices($arrData)
    {
        if (empty($arrData)) {
            return $this;
        }

        $adapter = $this->getWriteAdapter();
        $productIds = array();

        try {
            foreach ($arrData as $key => $data) {
                $productIds['product_id'] = $data['product_id'];
                $arrData[$key]['rule_date'] = $this->dateTime->date($data['rule_date'], false);
                $arrData[$key]['latest_start_date'] = $this->dateTime->date($data['latest_start_date'], false);
                $arrData[$key]['earliest_end_date'] = $this->dateTime->date($data['earliest_end_date'], false);
            }
            $adapter->insertOnDuplicate(
                $this->resource->getTableName('catalogrule_affected_product'),
                array_unique($productIds)
            );
            $adapter->insertOnDuplicate($this->resource->getTableName('catalogrule_product_price'), $arrData);
        } catch (\Exception $e) {
            throw $e;
        }

        return $this;
    }

    /**
     * Calculate product price based on price rule data and previous information
     *
     * @param array $ruleData
     * @param null|array $productData
     * @return float
     */
    protected function calcRuleProductPrice($ruleData, $productData = null)
    {
        if ($productData !== null && isset($productData['rule_price'])) {
            $productPrice = $productData['rule_price'];
        } else {
            $productPrice = $ruleData['default_price'];
        }

        switch ($ruleData['action_operator']) {
            case 'to_fixed':
                $productPrice = min($ruleData['action_amount'], $productPrice);
                break;
            case 'to_percent':
                $productPrice = $productPrice * $ruleData['action_amount'] / 100;
                break;
            case 'by_fixed':
                $productPrice = max(0, $productPrice - $ruleData['action_amount']);
                break;
            case 'by_percent':
                $productPrice = $productPrice * (1 - $ruleData['action_amount'] / 100);
                break;
            default:
                $productPrice = 0;
        }

        return $this->priceCurrency->round($productPrice);
    }

    protected function getRuleProductsStmt($fromDate, $toDate, $productId = null, $websiteId = null)
    {
        $read = $this->getReadAdapter();
        /**
         * Sort order is important
         * It used for check stop price rule condition.
         * website_id   customer_group_id   product_id  sort_order
         *  1           1                   1           0
         *  1           1                   1           1
         *  1           1                   1           2
         * if row with sort order 1 will have stop flag we should exclude
         * all next rows for same product id from price calculation
         */
        $select = $read->select()->from(
            array('rp' => $this->resource->getTableName('catalogrule_product'))
        )->where(
            $read->quoteInto(
                'rp.from_time = 0 or rp.from_time <= ?',
                $toDate
            ) . ' OR ' . $read->quoteInto(
                'rp.to_time = 0 or rp.to_time >= ?',
                $fromDate
            )
        )->order(
            array('rp.website_id', 'rp.customer_group_id', 'rp.product_id', 'rp.sort_order', 'rp.rule_id')
        );

        if (!is_null($productId)) {
            $select->where('rp.product_id=?', $productId);
        }

        /**
         * Join default price and websites prices to result
         */
        $priceAttr = $this->eavConfig->getAttribute(Product::ENTITY, 'price');
        $priceTable = $priceAttr->getBackend()->getTable();
        $attributeId = $priceAttr->getId();

        $joinCondition = '%1$s.entity_id=rp.product_id AND (%1$s.attribute_id=' .
            $attributeId .
            ') and %1$s.store_id=%2$s';

        $select->join(
            array('pp_default' => $priceTable),
            sprintf($joinCondition, 'pp_default', \Magento\Store\Model\Store::DEFAULT_STORE_ID),
            array()
        );

        $website = $this->storeManager->getWebsite($websiteId);
        $defaultGroup = $website->getDefaultGroup();
        if ($defaultGroup instanceof \Magento\Store\Model\Group) {
            $storeId = $defaultGroup->getDefaultStoreId();
        } else {
            $storeId = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        }

        $select->joinInner(
            array('product_website' => $this->resource->getTableName('catalog_product_website')),
            'product_website.product_id=rp.product_id ' .
            'AND rp.website_id=product_website.website_id ' .
            'AND product_website.website_id=' .
            $websiteId,
            array()
        );

        $tableAlias = 'pp' . $websiteId;
        $select->joinLeft(
            array($tableAlias => $priceTable),
            sprintf($joinCondition, $tableAlias, $storeId),
            array()
        );
        $select->columns([
            'default_price' => $this->getReadAdapter()->getIfNullSql($tableAlias . '.value', 'pp_default.value')
        ]);

        return $read->query($select);
    }


    protected function getMatchingProductIdsByRule(Rule $rule)
    {
        $productCollection = $this->productFactory->create()->getCollection();
        $this->sqlBuilder->attachConditionToCollection($productCollection, $rule->getConditions());
        $productIds = array();
        $allIds = array_unique($productCollection->getAllIds());
        foreach ($allIds as $productId) {
            if ($rule->getConditions()->validateByEntityId($productId)) {
                $productIds[] = $productId;
            }
        }

        return $productIds;
    }

    protected function applyToProduct(Rule $rule, $product)
    {
        if (!$rule->getIsActive()) {
            return $this;
        }

        $ruleId = $rule->getId();
        $productId = $product->getId();
        $websiteIds = array_intersect($product->getWebsiteIds(), $rule->getWebsiteIds());

        $write = $this->getWriteAdapter();

        $write->delete(
            $this->resource->getTableName('catalogrule_product'),
            array($write->quoteInto('rule_id=?', $ruleId), $write->quoteInto('product_id=?', $productId))
        );

        if (!$rule->getConditions()->validate($product)) {
            $write->delete(
                $this->resource->getTableName('catalogrule_product_price'),
                array($write->quoteInto('product_id=?', $productId))
            );
            return $this;
        }

        $customerGroupIds = $rule->getCustomerGroupIds();
        $fromTime = strtotime($rule->getFromDate());
        $toTime = strtotime($rule->getToDate());
        $toTime = $toTime ? $toTime + self::SECONDS_IN_DAY - 1 : 0;
        $sortOrder = (int)$rule->getSortOrder();
        $actionOperator = $rule->getSimpleAction();
        $actionAmount = $rule->getDiscountAmount();
        $actionStop = $rule->getStopRulesProcessing();
        $subActionOperator = $rule->getSubIsEnable() ? $rule->getSubSimpleAction() : '';
        $subActionAmount = $rule->getSubDiscountAmount();

        $rows = array();
        try {
            foreach ($websiteIds as $websiteId) {
                foreach ($customerGroupIds as $customerGroupId) {
                    $rows[] = array(
                        'rule_id' => $ruleId,
                        'from_time' => $fromTime,
                        'to_time' => $toTime,
                        'website_id' => $websiteId,
                        'customer_group_id' => $customerGroupId,
                        'product_id' => $productId,
                        'action_operator' => $actionOperator,
                        'action_amount' => $actionAmount,
                        'action_stop' => $actionStop,
                        'sort_order' => $sortOrder,
                        'sub_simple_action' => $subActionOperator,
                        'sub_discount_amount' => $subActionAmount
                    );

                    if (count($rows) == 1000) {
                        $write->insertMultiple($this->resource->getTableName('catalogrule_product'), $rows);
                        $rows = array();
                    }
                }
            }

            if (!empty($rows)) {
                $write->insertMultiple($this->resource->getTableName('catalogrule_product'), $rows);
            }
        } catch (\Exception $e) {
            throw $e;
        }

        $this->applyAllRulesForDateRange($product);

        return $this;
    }
}
