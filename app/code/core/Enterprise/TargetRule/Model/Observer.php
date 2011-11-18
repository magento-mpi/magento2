<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * TargetRule observer
 *
 */
class Enterprise_TargetRule_Model_Observer
{
    /**
     * Prepare target rule data
     *
     * @param Varien_Event_Observer $observer
     */
    public function prepareTargetRuleSave(Varien_Event_Observer $observer)
    {
        $_vars = array('targetrule_rule_based_positions', 'tgtr_position_behavior');
        $_varPrefix = array('related_', 'upsell_', 'crosssell_');
        if ($product = $observer->getEvent()->getProduct()) {
            foreach ($_vars as $var) {
                foreach ($_varPrefix as $pref) {
                    $v = $pref . $var;
                    if ($product->getData($v.'_default') == 1) {
                        $product->setData($v, null);
                    }
                }
            }
        }
    }

    /**
     * After Catalog Product Save - rebuild product index by rule conditions
     * and refresh cache index
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_TargetRule_Model_Observer
     */
    public function catalogProductAfterSave(Varien_Event_Observer $observer)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $observer->getEvent()->getProduct();

        Mage::getSingleton('Mage_Index_Model_Indexer')->logEvent(
            new Varien_Object(array(
                'id' => $product->getId(),
                'store_id' => $product->getStoreId(),
                'rule' => $product->getData('rule'),
                'from_date' => $product->getData('from_date'),
                'to_date' => $product->getData('to_date')
            )),
            Enterprise_TargetRule_Model_Index::ENTITY_PRODUCT,
            Enterprise_TargetRule_Model_Index::EVENT_TYPE_REINDEX_PRODUCTS
        );
        return $this;
    }

    /**
     * Process event on 'save_commit_after' event
     *
     * @param Varien_Event_Observer $observer
     */
    public function catalogProductSaveCommitAfter(Varien_Event_Observer $observer)
    {
        Mage::getSingleton('Mage_Index_Model_Indexer')->indexEvents(
            Enterprise_TargetRule_Model_Index::ENTITY_PRODUCT,
            Enterprise_TargetRule_Model_Index::EVENT_TYPE_REINDEX_PRODUCTS
        );
    }
}
