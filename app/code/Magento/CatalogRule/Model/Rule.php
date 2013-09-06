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
 * @method Magento_CatalogRule_Model_Resource_Rule _getResource()
 * @method Magento_CatalogRule_Model_Resource_Rule getResource()
 * @method string getName()
 * @method Magento_CatalogRule_Model_Rule setName(string $value)
 * @method string getDescription()
 * @method Magento_CatalogRule_Model_Rule setDescription(string $value)
 * @method string getFromDate()
 * @method Magento_CatalogRule_Model_Rule setFromDate(string $value)
 * @method string getToDate()
 * @method Magento_CatalogRule_Model_Rule setToDate(string $value)
 * @method Magento_CatalogRule_Model_Rule setCustomerGroupIds(string $value)
 * @method int getIsActive()
 * @method Magento_CatalogRule_Model_Rule setIsActive(int $value)
 * @method string getConditionsSerialized()
 * @method Magento_CatalogRule_Model_Rule setConditionsSerialized(string $value)
 * @method string getActionsSerialized()
 * @method Magento_CatalogRule_Model_Rule setActionsSerialized(string $value)
 * @method int getStopRulesProcessing()
 * @method Magento_CatalogRule_Model_Rule setStopRulesProcessing(int $value)
 * @method int getSortOrder()
 * @method Magento_CatalogRule_Model_Rule setSortOrder(int $value)
 * @method string getSimpleAction()
 * @method Magento_CatalogRule_Model_Rule setSimpleAction(string $value)
 * @method float getDiscountAmount()
 * @method Magento_CatalogRule_Model_Rule setDiscountAmount(float $value)
 * @method string getWebsiteIds()
 * @method Magento_CatalogRule_Model_Rule setWebsiteIds(string $value)
 *
 * @category    Magento
 * @package     Magento_CatalogRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogRule_Model_Rule extends Magento_Rule_Model_Abstract
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
     * @var Magento_CatalogRule_Helper_Data
     */
    protected $_catalogRuleData = null;

    /**
     * @param Magento_CatalogRule_Helper_Data $catalogRuleData
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Model_Context $context
     * @param Magento_CatalogRule_Model_Resource_Rule $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_CatalogRule_Helper_Data $catalogRuleData,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Model_Context $context,
        Magento_CatalogRule_Model_Resource_Rule $resource,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_catalogRuleData = $catalogRuleData;
        parent::__construct($formFactory, $context, $resource, $resourceCollection, $data);
    }

    /**
     * Init resource model and id field
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento_CatalogRule_Model_Resource_Rule');
        $this->setIdFieldName('rule_id');
    }

    /**
     * Getter for rule conditions collection
     *
     * @return Magento_CatalogRule_Model_Rule_Condition_Combine
     */
    public function getConditionsInstance()
    {
        return Mage::getModel('Magento_CatalogRule_Model_Rule_Condition_Combine');
    }

    /**
     * Getter for rule actions collection
     *
     * @return Magento_CatalogRule_Model_Rule_Action_Collection
     */
    public function getActionsInstance()
    {
        return Mage::getModel('Magento_CatalogRule_Model_Rule_Action_Collection');
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
                /** @var $productCollection Magento_Catalog_Model_Resource_Product_Collection */
                $productCollection = Mage::getResourceModel('Magento_Catalog_Model_Resource_Product_Collection');
                $productCollection->addWebsiteFilter($this->getWebsiteIds());
                if ($this->_productsFilter) {
                    $productCollection->addIdFilter($this->_productsFilter);
                }
                $this->getConditions()->collectValidatedAttributes($productCollection);

                Mage::getSingleton('Magento_Core_Model_Resource_Iterator')->walk(
                    $productCollection->getSelect(),
                    array(array($this, 'callbackValidateProduct')),
                    array(
                        'attributes' => $this->getCollectedAttributes(),
                        'product'    => Mage::getModel('Magento_Catalog_Model_Product'),
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
     * @param int|Magento_Catalog_Model_Product $product
     * @param array|null $websiteIds
     *
     * @return void
     */
    public function applyToProduct($product, $websiteIds = null)
    {
        if (is_numeric($product)) {
            $product = Mage::getModel('Magento_Catalog_Model_Product')->load($product);
        }
        if (is_null($websiteIds)) {
            $websiteIds = $this->getWebsiteIds();
        }
        $this->getResource()->applyToProduct($this, $product, $websiteIds);
    }

    /**
     * Apply all price rules, invalidate related cache and refresh price index
     *
     * @return Magento_CatalogRule_Model_Rule
     */
    public function applyAll()
    {
        $this->getResourceCollection()->walk(array($this->_getResource(), 'updateRuleProductData'));
        $this->_getResource()->applyAllRulesForDateRange();
        $this->_invalidateCache();
        $indexProcess = Mage::getSingleton('Magento_Index_Model_Indexer')->getProcessByCode('catalog_product_price');
        if ($indexProcess) {
            $indexProcess->reindexAll();
        }
    }

    /**
     * Apply all price rules to product
     *
     * @param  int|Magento_Catalog_Model_Product $product
     * @return Magento_CatalogRule_Model_Rule
     */
    public function applyAllRulesToProduct($product)
    {
        $this->_getResource()->applyAllRulesForDateRange(NULL, NULL, $product);
        $this->_invalidateCache();

        if ($product instanceof Magento_Catalog_Model_Product) {
            $productId = $product->getId();
        } else {
            $productId = $product;
        }

        if ($productId) {
            Mage::getSingleton('Magento_Index_Model_Indexer')->processEntityAction(
                new Magento_Object(array('id' => $productId)),
                Magento_Catalog_Model_Product::ENTITY,
                Magento_Catalog_Model_Product_Indexer_Price::EVENT_TYPE_REINDEX_PRICE
            );
        }
    }

    /**
     * Calculate price using catalog price rule of product
     *
     * @param Magento_Catalog_Model_Product $product
     * @param float $price
     * @return float|null
     */
    public function calcProductPriceRule(Magento_Catalog_Model_Product $product, $price)
    {
        $priceRules = null;
        $productId  = $product->getId();
        $storeId    = $product->getStoreId();
        $websiteId  = Mage::app()->getStore($storeId)->getWebsiteId();
        if ($product->hasCustomerGroupId()) {
            $customerGroupId = $product->getCustomerGroupId();
        } else {
            $customerGroupId = Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerGroupId();
        }
        $dateTs     = Mage::app()->getLocale()->storeTimeStamp($storeId);
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
     * @return Magento_CatalogRule_Model_Rule
     */
    protected function _invalidateCache()
    {
        $types = Mage::getConfig()->getNode(self::XML_NODE_RELATED_CACHE);
        if ($types) {
            $types = $types->asArray();
            /** @var Magento_Core_Model_Cache_TypeListInterface $cacheTypeList */
            $cacheTypeList = Mage::getObjectManager()->get('Magento_Core_Model_Cache_TypeListInterface');
            $cacheTypeList->invalidate(array_keys($types));
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
    public function toString($format='')
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
