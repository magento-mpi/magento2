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
     * @param Magento_Core_Model_Registry $coreRegistry
     */
    public function __construct(
        Magento_Core_Model_Registry $coreRegistry
    ) {
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

        $rules = Mage::getModel('Magento_CatalogRule_Model_Rule')->getCollection()
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
        Mage::getModel('Magento_CatalogRule_Model_Flag')->loadSelf()
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
        Mage::getModel('Magento_CatalogRule_Model_Rule')->applyAll();
        Mage::getModel('Magento_CatalogRule_Model_Flag')->loadSelf()
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
            $gId = Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerGroupId();
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
            $productPriceRule = Mage::getModel('Magento_CatalogRule_Model_Rule')
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

        Mage::getSingleton('Magento_CatalogRule_Model_Rule_Product_Price')
            ->applyPriceRuleToIndexTable($select, $indexTable, $entityId, $customerGroupId, $websiteId,
                $updateFields, $websiteDate);

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
            Mage::getModel('Magento_CatalogRule_Model_Rule')->applyAll();
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addWarning(
                __('%1 Catalog Price Rules based on "%2" attribute have been disabled.', $disabledRulesCount, $attributeCode));
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
            /* @var $session Magento_Customer_Model_Session */
            $session = Mage::getSingleton('Magento_Customer_Model_Session');
            if ($session->isLoggedIn()) {
                $groupId = Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerGroupId();
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

        $rules = Mage::getModel('Magento_CatalogRule_Model_Rule')->getCollection()
            ->addFieldToFilter('is_active', 1);

        foreach ($rules as $rule) {
            $rule->setProductsFilter($affectedEntityIds);
            Mage::getResourceSingleton('Magento_CatalogRule_Model_Resource_Rule')->updateRuleProductData($rule);
        }
    }
}
