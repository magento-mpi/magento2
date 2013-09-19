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
 * Catalog Price rules observer model
 */
class Magento_CatalogRule_Model_Observer
{
    /**
     * Store calculated catalog rules prices for products
     * Prices collected per website, customer group, date and product
     *
     * @var array
     */
    protected $_rulePrices = array();

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @var Magento_CatalogRule_Model_Rule_Product_Price
     */
    protected $_productPrice;

    /**
     * @var Magento_Backend_Model_Session
     */
    protected $_backendSession;

    /**
     * @var Magento_CatalogRule_Model_RuleFactory
     */
    protected $_ruleFactory;

    /**
     * @var Magento_CatalogRule_Model_FlagFactory
     */
    protected $_flagFactory;

    /**
     * @var Magento_CatalogRule_Model_Resource_Rule_CollectionFactory
     */
    protected $_ruleCollFactory;

    /**
     * @param Magento_CatalogRule_Model_RuleFactory $ruleFactory
     * @param Magento_CatalogRule_Model_Resource_Rule_CollectionFactory $ruleCollFactory
     * @param Magento_CatalogRule_Model_FlagFactory $flagFactory
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Backend_Model_Session $backendSession
     * @param Magento_CatalogRule_Model_Rule_Product_Price $productPrice
     * @param Magento_Core_Model_Registry $coreRegistry
     */
    public function __construct(
        Magento_CatalogRule_Model_RuleFactory $ruleFactory,
        Magento_CatalogRule_Model_Resource_Rule_CollectionFactory $ruleCollFactory,
        Magento_CatalogRule_Model_FlagFactory $flagFactory,
        Magento_Customer_Model_Session $customerSession,
        Magento_Backend_Model_Session $backendSession,
        Magento_CatalogRule_Model_Rule_Product_Price $productPrice,
        Magento_Core_Model_Registry $coreRegistry
    ) {
        $this->_ruleFactory = $ruleFactory;
        $this->_flagFactory = $flagFactory;
        $this->_ruleCollFactory = $ruleCollFactory;
        $this->_customerSession = $customerSession;
        $this->_backendSession = $backendSession;
        $this->_productPrice = $productPrice;
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * Apply all catalog price rules for specific product
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_CatalogRule_Model_Observer
     */
    public function applyAllRulesOnProduct($observer)
    {
        $product = $observer->getEvent()->getProduct();
        if ($product->getIsMassupdate()) {
            return;
        }

        $productWebsiteIds = $product->getWebsiteIds();

        $rules = $this->_ruleCollFactory->create()
            ->addFieldToFilter('is_active', 1);

        foreach ($rules as $rule) {
            $websiteIds = array_intersect($productWebsiteIds, $rule->getWebsiteIds());
            $rule->applyToProduct($product, $websiteIds);
        }

        return $this;
    }

    /**
     * Apply all price rules for current date.
     * Handle cataolg_product_import_after event
     *
     * @param   Magento_Event_Observer $observer
     *
     * @return  Magento_CatalogRule_Model_Observer
     */
    public function applyAllRules($observer)
    {
        $resource = Mage::getResourceSingleton('Magento_CatalogRule_Model_Resource_Rule');
        $resource->applyAllRulesForDateRange($resource->formatDate(mktime(0,0,0)));
        $this->_flagFactory->create()
            ->loadSelf()
            ->setState(0)
            ->save();

        return $this;
    }

    /**
     * Apply all catalog price rules
     *
     * Fire the same name process as catalog rule model
     * Event name "apply_catalog_price_rules"
     *
     * @param  Magento_Event_Observer $observer
     * @return Magento_CatalogRule_Model_Observer
     */
    public function processApplyAll(Magento_Event_Observer $observer)
    {
        $this->_ruleFactory->create()->applyAll();
        $this->_flagFactory->create()
            ->loadSelf()
            ->setState(0)
            ->save();
        return $this;
    }

    /**
     * Apply catalog price rules to product on frontend
     *
     * @param   Magento_Event_Observer $observer
     *
     * @return  Magento_CatalogRule_Model_Observer
     */
    public function processFrontFinalPrice($observer)
    {
        $product    = $observer->getEvent()->getProduct();
        $pId        = $product->getId();
        $storeId    = $product->getStoreId();

        if ($observer->hasDate()) {
            $date = $observer->getEvent()->getDate();
        } else {
            $date = Mage::app()->getLocale()->storeTimeStamp($storeId);
        }

        if ($observer->hasWebsiteId()) {
            $wId = $observer->getEvent()->getWebsiteId();
        } else {
            $wId = Mage::app()->getStore($storeId)->getWebsiteId();
        }

        if ($observer->hasCustomerGroupId()) {
            $gId = $observer->getEvent()->getCustomerGroupId();
        } elseif ($product->hasCustomerGroupId()) {
            $gId = $product->getCustomerGroupId();
        } else {
            $gId = $this->_customerSession->getCustomerGroupId();
        }

        $key = "$date|$wId|$gId|$pId";
        if (!isset($this->_rulePrices[$key])) {
            $rulePrice = Mage::getResourceModel('Magento_CatalogRule_Model_Resource_Rule')
                ->getRulePrice($date, $wId, $gId, $pId);
            $this->_rulePrices[$key] = $rulePrice;
        }
        if ($this->_rulePrices[$key]!==false) {
            $finalPrice = min($product->getData('final_price'), $this->_rulePrices[$key]);
            $product->setFinalPrice($finalPrice);
        }
        return $this;
    }

    /**
     * Apply catalog price rules to product in admin
     *
     * @param   Magento_Event_Observer $observer
     *
     * @return  Magento_CatalogRule_Model_Observer
     */
    public function processAdminFinalPrice($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $storeId = $product->getStoreId();
        $date = Mage::app()->getLocale()->storeDate($storeId);
        $key = false;

        $ruleData = $this->_coreRegistry->registry('rule_data');
        if ($ruleData) {
            $wId = $ruleData->getWebsiteId();
            $gId = $ruleData->getCustomerGroupId();
            $pId = $product->getId();

            $key = "$date|$wId|$gId|$pId";
        } elseif (!is_null($product->getWebsiteId()) && !is_null($product->getCustomerGroupId())) {
            $wId = $product->getWebsiteId();
            $gId = $product->getCustomerGroupId();
            $pId = $product->getId();
            $key = "$date|$wId|$gId|$pId";
        }

        if ($key) {
            if (!isset($this->_rulePrices[$key])) {
                $rulePrice = Mage::getResourceModel('Magento_CatalogRule_Model_Resource_Rule')
                    ->getRulePrice($date, $wId, $gId, $pId);
                $this->_rulePrices[$key] = $rulePrice;
            }
            if ($this->_rulePrices[$key]!==false) {
                $finalPrice = min($product->getData('final_price'), $this->_rulePrices[$key]);
                $product->setFinalPrice($finalPrice);
            }
        }

        return $this;
    }

    /**
     * Calculate price using catalog price rules of configurable product
     *
     * @param Magento_Event_Observer $observer
     *
     * @return Magento_CatalogRule_Model_Observer
     */
    public function catalogProductTypeConfigurablePrice(Magento_Event_Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        if ($product instanceof Magento_Catalog_Model_Product
            && $product->getConfigurablePrice() !== null
        ) {
            $configurablePrice = $product->getConfigurablePrice();
            $productPriceRule = $this->_ruleFactory->create()
                ->calcProductPriceRule($product, $configurablePrice);
            if ($productPriceRule !== null) {
                $product->setConfigurablePrice($productPriceRule);
            }
        }

        return $this;
    }

    /**
     * Daily update catalog price rule by cron
     * Update include interval 3 days - current day - 1 days before + 1 days after
     * This method is called from cron process, cron is working in UTC time and
     * we should generate data for interval -1 day ... +1 day
     *
     * @param   Magento_Event_Observer $observer
     *
     * @return  Magento_CatalogRule_Model_Observer
     */
    public function dailyCatalogUpdate($observer)
    {
        Mage::getResourceSingleton('Magento_CatalogRule_Model_Resource_Rule')->applyAllRulesForDateRange();

        return $this;
    }

    /**
     * Clean out calculated catalog rule prices for products
     */
    public function flushPriceCache()
    {
        $this->_rulePrices = array();
    }

    /**
     * Calculate minimal final price with catalog rule price
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_CatalogRule_Model_Observer
     */
    public function prepareCatalogProductPriceIndexTable(Magento_Event_Observer $observer)
    {
        $select             = $observer->getEvent()->getSelect();

        $indexTable         = $observer->getEvent()->getIndexTable();
        $entityId           = $observer->getEvent()->getEntityId();
        $customerGroupId    = $observer->getEvent()->getCustomerGroupId();
        $websiteId          = $observer->getEvent()->getWebsiteId();
        $websiteDate        = $observer->getEvent()->getWebsiteDate();
        $updateFields       = $observer->getEvent()->getUpdateFields();

        $this->_productPrice->applyPriceRuleToIndexTable(
            $select, $indexTable, $entityId, $customerGroupId, $websiteId, $updateFields, $websiteDate
        );

        return $this;
    }

    /**
     * Check rules that contains affected attribute
     * If rules were found they will be set to inactive and notice will be add to admin session
     *
     * @param string $attributeCode
     *
     * @return Magento_CatalogRule_Model_Observer
     */
    protected function _checkCatalogRulesAvailability($attributeCode)
    {
        /* @var $collection Magento_CatalogRule_Model_Resource_Rule_Collection */
        $collection = Mage::getResourceModel('Magento_CatalogRule_Model_Resource_Rule_Collection')
            ->addAttributeInConditionFilter($attributeCode);

        $disabledRulesCount = 0;
        foreach ($collection as $rule) {
            /* @var $rule Magento_CatalogRule_Model_Rule */
            $rule->setIsActive(0);
            /* @var $rule->getConditions() Magento_CatalogRule_Model_Rule_Condition_Combine */
            $this->_removeAttributeFromConditions($rule->getConditions(), $attributeCode);
            $rule->save();

            $disabledRulesCount++;
        }

        if ($disabledRulesCount) {
            $this->_ruleFactory->create()->applyAll();
            $this->_backendSession->addWarning(
                __('%1 Catalog Price Rules based on "%2" attribute have been disabled.', $disabledRulesCount, $attributeCode)
            );
        }

        return $this;
    }

    /**
     * Remove catalog attribute condition by attribute code from rule conditions
     *
     * @param Magento_CatalogRule_Model_Rule_Condition_Combine $combine
     *
     * @param string $attributeCode
     */
    protected function _removeAttributeFromConditions($combine, $attributeCode)
    {
        $conditions = $combine->getConditions();
        foreach ($conditions as $conditionId => $condition) {
            if ($condition instanceof Magento_CatalogRule_Model_Rule_Condition_Combine) {
                $this->_removeAttributeFromConditions($condition, $attributeCode);
            }
            if ($condition instanceof Magento_Rule_Model_Condition_Product_Abstract) {
                if ($condition->getAttribute() == $attributeCode) {
                    unset($conditions[$conditionId]);
                }
            }
        }
        $combine->setConditions($conditions);
    }

    /**
     * After save attribute if it is not used for promo rules already check rules for containing this attribute
     *
     * @param Magento_Event_Observer $observer
     *
     * @return Magento_CatalogRule_Model_Observer
     */
    public function catalogAttributeSaveAfter(Magento_Event_Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute->dataHasChangedFor('is_used_for_promo_rules') && !$attribute->getIsUsedForPromoRules()) {
            $this->_checkCatalogRulesAvailability($attribute->getAttributeCode());
        }

        return $this;
    }

    /**
     * After delete attribute check rules that contains deleted attribute
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_CatalogRule_Model_Observer
     */
    public function catalogAttributeDeleteAfter(Magento_Event_Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute->getIsUsedForPromoRules()) {
            $this->_checkCatalogRulesAvailability($attribute->getAttributeCode());
        }

        return $this;
    }

    public function prepareCatalogProductCollectionPrices(Magento_Event_Observer $observer)
    {
        /* @var $collection Magento_Catalog_Model_Resource_Product_Collection */
        $collection = $observer->getEvent()->getCollection();
        $store      = Mage::app()->getStore($observer->getEvent()->getStoreId());
        $websiteId  = $store->getWebsiteId();
        if ($observer->getEvent()->hasCustomerGroupId()) {
            $groupId = $observer->getEvent()->getCustomerGroupId();
        } else {
            if ($this->_customerSession->isLoggedIn()) {
                $groupId = $this->_customerSession->getCustomerGroupId();
            } else {
                $groupId = Magento_Customer_Model_Group::NOT_LOGGED_IN_ID;
            }
        }
        if ($observer->getEvent()->hasDate()) {
            $date = $observer->getEvent()->getDate();
        } else {
            $date = Mage::app()->getLocale()->storeTimeStamp($store);
        }

        $productIds = array();
        /* @var $product Magento_Catalog_Model_Product */
        foreach ($collection as $product) {
            $key = implode('|', array($date, $websiteId, $groupId, $product->getId()));
            if (!isset($this->_rulePrices[$key])) {
                $productIds[] = $product->getId();
            }
        }

        if ($productIds) {
            $rulePrices = Mage::getResourceModel('Magento_CatalogRule_Model_Resource_Rule')
                ->getRulePrices($date, $websiteId, $groupId, $productIds);
            foreach ($productIds as $productId) {
                $key = implode('|', array($date, $websiteId, $groupId, $productId));
                $this->_rulePrices[$key] = isset($rulePrices[$productId]) ? $rulePrices[$productId] : false;
            }
        }

        return $this;
    }

    /**
     * Create catalog rule relations for imported products
     *
     * @param Magento_Event_Observer $observer
     */
    public function createCatalogRulesRelations(Magento_Event_Observer $observer)
    {
        $adapter = $observer->getEvent()->getAdapter();
        $affectedEntityIds = $adapter->getAffectedEntityIds();

        if (empty($affectedEntityIds)) {
            return;
        }

        $rules = $this->_ruleCollFactory->create()
            ->addFieldToFilter('is_active', 1);

        foreach ($rules as $rule) {
            $rule->setProductsFilter($affectedEntityIds);
            Mage::getResourceSingleton('Magento_CatalogRule_Model_Resource_Rule')->updateRuleProductData($rule);
        }
    }
}
