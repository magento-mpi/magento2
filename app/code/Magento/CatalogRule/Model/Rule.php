<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Rule data model
 *
 * @method \Magento\CatalogRule\Model\Resource\Rule _getResource()
 * @method \Magento\CatalogRule\Model\Resource\Rule getResource()
 * @method string getName()
 * @method \Magento\CatalogRule\Model\Rule setName(string $value)
 * @method string getDescription()
 * @method \Magento\CatalogRule\Model\Rule setDescription(string $value)
 * @method string getFromDate()
 * @method \Magento\CatalogRule\Model\Rule setFromDate(string $value)
 * @method string getToDate()
 * @method \Magento\CatalogRule\Model\Rule setToDate(string $value)
 * @method \Magento\CatalogRule\Model\Rule setCustomerGroupIds(string $value)
 * @method int getIsActive()
 * @method \Magento\CatalogRule\Model\Rule setIsActive(int $value)
 * @method string getConditionsSerialized()
 * @method \Magento\CatalogRule\Model\Rule setConditionsSerialized(string $value)
 * @method string getActionsSerialized()
 * @method \Magento\CatalogRule\Model\Rule setActionsSerialized(string $value)
 * @method int getStopRulesProcessing()
 * @method \Magento\CatalogRule\Model\Rule setStopRulesProcessing(int $value)
 * @method int getSortOrder()
 * @method \Magento\CatalogRule\Model\Rule setSortOrder(int $value)
 * @method string getSimpleAction()
 * @method \Magento\CatalogRule\Model\Rule setSimpleAction(string $value)
 * @method float getDiscountAmount()
 * @method \Magento\CatalogRule\Model\Rule setDiscountAmount(float $value)
 * @method string getWebsiteIds()
 * @method \Magento\CatalogRule\Model\Rule setWebsiteIds(string $value)
 *
 * @category    Magento
 * @package     Magento_CatalogRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogRule\Model;

class Rule extends \Magento\Rule\Model\AbstractModel
{
    /**
     * Related cache types config path
     */
    const XML_NODE_RELATED_CACHE = 'global/catalogrule/related_cache_types';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'catalogrule_rule';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getRule() in this case
     *
     * @var string
     */
    protected $_eventObject = 'rule';

    /**
     * Store matched product Ids
     *
     * @var array
     */
    protected $_productIds;

    /**
     * Limitation for products collection
     *
     * @var int|array|null
     */
    protected $_productsFilter = null;

    /**
     * Store current date at "Y-m-d H:i:s" format
     *
     * @var string
     */
    protected $_now;

    /**
     * Cached data of prices calculated by price rules
     *
     * @var array
     */
    protected static $_priceRulesData = array();

    /**
     * Catalog rule data
     *
     * @var \Magento\CatalogRule\Helper\Data
     */
    protected $_catalogRuleData;

    /**
     * @var \Magento\Core\Model\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_coreConfig;

    /**
     * @param \Magento\CatalogRule\Helper\Data $catalogRuleData
     * @param \Magento\Data\Form\Factory $formFactory
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Core\Model\Config $coreConfig
     * @param \Magento\CatalogRule\Model\Resource\Rule $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\CatalogRule\Helper\Data $catalogRuleData,
        \Magento\Data\Form\Factory $formFactory,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\Cache\TypeListInterface $cacheTypeList,
        \Magento\Core\Model\Config $coreConfig,
        \Magento\CatalogRule\Model\Resource\Rule $resource,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_catalogRuleData = $catalogRuleData;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_coreConfig = $coreConfig;
        parent::__construct($formFactory, $context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Init resource model and id field
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento\CatalogRule\Model\Resource\Rule');
        $this->setIdFieldName('rule_id');
    }

    /**
     * Getter for rule conditions collection
     *
     * @return \Magento\CatalogRule\Model\Rule\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return \Mage::getModel('Magento\CatalogRule\Model\Rule\Condition\Combine');
    }

    /**
     * Getter for rule actions collection
     *
     * @return \Magento\CatalogRule\Model\Rule\Action\Collection
     */
    public function getActionsInstance()
    {
        return \Mage::getModel('Magento\CatalogRule\Model\Rule\Action\Collection');
    }

    /**
     * Get catalog rule customer group Ids
     *
     * @return array
     */
    public function getCustomerGroupIds()
    {
        if (!$this->hasCustomerGroupIds()) {
            $customerGroupIds = $this->_getResource()->getCustomerGroupIds($this->getId());
            $this->setData('customer_group_ids', (array)$customerGroupIds);
        }
        return $this->_getData('customer_group_ids');
    }

    /**
     * Retrieve current date for current rule
     *
     * @return string
     */
    public function getNow()
    {
        if (!$this->_now) {
            return now();
        }
        return $this->_now;
    }

    /**
     * Set current date for current rule
     *
     * @param string $now
     */
    public function setNow($now)
    {
        $this->_now = $now;
    }

    /**
     * Get array of product ids which are matched by rule
     *
     * @return array
     */
    public function getMatchingProductIds()
    {
        if (is_null($this->_productIds)) {
            $this->_productIds = array();
            $this->setCollectedAttributes(array());

            if ($this->getWebsiteIds()) {
                /** @var $productCollection \Magento\Catalog\Model\Resource\Product\Collection */
                $productCollection = \Mage::getResourceModel('Magento\Catalog\Model\Resource\Product\Collection');
                $productCollection->addWebsiteFilter($this->getWebsiteIds());
                if ($this->_productsFilter) {
                    $productCollection->addIdFilter($this->_productsFilter);
                }
                $this->getConditions()->collectValidatedAttributes($productCollection);

                \Mage::getSingleton('Magento\Core\Model\Resource\Iterator')->walk(
                    $productCollection->getSelect(),
                    array(array($this, 'callbackValidateProduct')),
                    array(
                        'attributes' => $this->getCollectedAttributes(),
                        'product'    => \Mage::getModel('Magento\Catalog\Model\Product'),
                    )
                );
            }
        }

        return $this->_productIds;
    }

    /**
     * Callback function for product matching
     *
     * @param $args
     * @return void
     */
    public function callbackValidateProduct($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);

        if ($this->getConditions()->validate($product)) {
            $this->_productIds[] = $product->getId();
        }
    }

    /**
     * Apply rule to product
     *
     * @param int|\Magento\Catalog\Model\Product $product
     * @param array|null $websiteIds
     *
     * @return void
     */
    public function applyToProduct($product, $websiteIds = null)
    {
        if (is_numeric($product)) {
            $product = \Mage::getModel('Magento\Catalog\Model\Product')->load($product);
        }
        if (is_null($websiteIds)) {
            $websiteIds = $this->getWebsiteIds();
        }
        $this->getResource()->applyToProduct($this, $product, $websiteIds);
    }

    /**
     * Apply all price rules, invalidate related cache and refresh price index
     *
     * @return \Magento\CatalogRule\Model\Rule
     */
    public function applyAll()
    {
        $this->getResourceCollection()->walk(array($this->_getResource(), 'updateRuleProductData'));
        $this->_getResource()->applyAllRulesForDateRange();
        $this->_invalidateCache();
        $indexProcess = \Mage::getSingleton('Magento\Index\Model\Indexer')->getProcessByCode('catalog_product_price');
        if ($indexProcess) {
            $indexProcess->reindexAll();
        }
    }

    /**
     * Apply all price rules to product
     *
     * @param  int|\Magento\Catalog\Model\Product $product
     * @return \Magento\CatalogRule\Model\Rule
     */
    public function applyAllRulesToProduct($product)
    {
        $this->_getResource()->applyAllRulesForDateRange(null, null, $product);
        $this->_invalidateCache();

        if ($product instanceof \Magento\Catalog\Model\Product) {
            $productId = $product->getId();
        } else {
            $productId = $product;
        }

        if ($productId) {
            \Mage::getSingleton('Magento\Index\Model\Indexer')->processEntityAction(
                new \Magento\Object(array('id' => $productId)),
                \Magento\Catalog\Model\Product::ENTITY,
                \Magento\Catalog\Model\Product\Indexer\Price::EVENT_TYPE_REINDEX_PRICE
            );
        }
    }

    /**
     * Calculate price using catalog price rule of product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param float $price
     * @return float|null
     */
    public function calcProductPriceRule(\Magento\Catalog\Model\Product $product, $price)
    {
        $priceRules = null;
        $productId  = $product->getId();
        $storeId    = $product->getStoreId();
        $websiteId  = \Mage::app()->getStore($storeId)->getWebsiteId();
        if ($product->hasCustomerGroupId()) {
            $customerGroupId = $product->getCustomerGroupId();
        } else {
            $customerGroupId = \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerGroupId();
        }
        $dateTs     = \Mage::app()->getLocale()->storeTimeStamp($storeId);
        $cacheKey   = date('Y-m-d', $dateTs) . "|$websiteId|$customerGroupId|$productId|$price";

        if (!array_key_exists($cacheKey, self::$_priceRulesData)) {
            $rulesData = $this->_getRulesFromProduct($dateTs, $websiteId, $customerGroupId, $productId);
            if ($rulesData) {
                foreach ($rulesData as $ruleData) {
                    if ($product->getParentId()) {
                        if (!empty($ruleData['sub_simple_action'])) {
                            $priceRules = $this->_catalogRuleData->calcPriceRule(
                                $ruleData['sub_simple_action'],
                                $ruleData['sub_discount_amount'],
                                $priceRules ? $priceRules : $price
                            );
                        } else {
                            $priceRules = $priceRules ? $priceRules : $price;
                        }
                        if ($ruleData['action_stop']) {
                            break;
                        }
                    } else {
                        $priceRules = $this->_catalogRuleData->calcPriceRule(
                            $ruleData['action_operator'],
                            $ruleData['action_amount'],
                            $priceRules ? $priceRules : $price
                        );
                        if ($ruleData['action_stop']) {
                            break;
                        }
                    }
                }
                return self::$_priceRulesData[$cacheKey] = $priceRules;
            } else {
                self::$_priceRulesData[$cacheKey] = null;
            }
        } else {
            return self::$_priceRulesData[$cacheKey];
        }
        return null;
    }

    /**
     * Get rules from product
     *
     * @param string $dateTs
     * @param int $websiteId
     * @param array $customerGroupId
     * @param int $productId
     * @return array
     */
    protected function _getRulesFromProduct($dateTs, $websiteId, $customerGroupId, $productId)
    {
        return $this->_getResource()->getRulesFromProduct($dateTs, $websiteId, $customerGroupId, $productId);
    }

    /**
     * Filtering products that must be checked for matching with rule
     *
     * @param  int|array $productIds
     */
    public function setProductsFilter($productIds)
    {
        $this->_productsFilter = $productIds;
    }

    /**
     * Returns products filter
     *
     * @return array|int|null
     */
    public function getProductsFilter()
    {
        return $this->_productsFilter;
    }

    /**
     * Invalidate related cache types
     *
     * @return \Magento\CatalogRule\Model\Rule
     */
    protected function _invalidateCache()
    {
        $types = $this->_coreConfig->getNode(self::XML_NODE_RELATED_CACHE);
        if ($types) {
            $types = $types->asArray();
            $this->_cacheTypeList->invalidate(array_keys($types));
        }
        return $this;
    }

    /**
     * @deprecated after 1.11.2.0
     *
     * @param string $format
     *
     * @return string
     */
    public function toString($format = '')
    {
        return '';
    }

    /**
     * Returns rule as an array for admin interface
     *
     * @deprecated after 1.11.2.0
     *
     * @param array $arrAttributes
     *
     * Output example:
     * array(
     *   'name'=>'Example rule',
     *   'conditions'=>{condition_combine::toArray}
     *   'actions'=>{action_collection::toArray}
     * )
     *
     * @return array
     */
    public function toArray(array $arrAttributes = array())
    {
        return parent::toArray($arrAttributes);
    }
}
