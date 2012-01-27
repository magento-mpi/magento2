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
 * TargetRule Rule Resource Model
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_TargetRule_Model_Resource_Rule extends Mage_Rule_Model_Resource_Abstract
{
    /**
     * Store associated with rule entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = array(
        'product' => array(
            'associations_table' => 'enterprise_targetrule_product',
            'rule_id_field'      => 'rule_id',
            'entity_id_field'    => 'product_id'
        )
    );

   /**
    * Initialize main table and table id field
    */
    protected function _construct()
    {
        $this->_init('enterprise_targetrule', 'rule_id');
    }

    /**
     * Save matched products for current rule and clean index
     *
     * @param Mage_Core_Model_Abstract|Enterprise_TargetRule_Model_Rule $object
     *
     * @return Enterprise_TargetRule_Model_Resource_Rule
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        parent::_afterSave($object);

        $this->unbindRuleFromEntity($object->getId(), array(), 'product');
        $this->bindRuleToEntity($object->getId(), $object->getMatchingProductIds(), 'product');

        $typeId = (!$object->isObjectNew() && $object->getOrigData('apply_to') != $object->getData('apply_to'))
            ? null
            : $object->getData('apply_to');

        Mage::getSingleton('Mage_Index_Model_Indexer')->processEntityAction(
            new Varien_Object(array('type_id' => $typeId)),
            Enterprise_TargetRule_Model_Index::ENTITY_TARGETRULE,
            Enterprise_TargetRule_Model_Index::EVENT_TYPE_CLEAN_TARGETRULES
        );

        return $this;
    }

    /**
     * Clean index
     *
     * @param Mage_Core_Model_Abstract|Enterprise_TargetRule_Model_Rule $object
     *
     * @return Enterprise_TargetRule_Model_Resource_Rule
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        Mage::getSingleton('Mage_Index_Model_Indexer')->processEntityAction(
            new Varien_Object(array('type_id' => $object->getData('apply_to'))),
            Enterprise_TargetRule_Model_Index::ENTITY_TARGETRULE,
            Enterprise_TargetRule_Model_Index::EVENT_TYPE_CLEAN_TARGETRULES
        );

        parent::_beforeDelete($object);
        return $this;
    }





    /**
     * Prepare and Save Matched products for Rule
     *
     * @deprecated after 1.11.2.0
     *
     * @param Enterprise_TargetRule_Model_Rule $object
     *
     * @return Enterprise_TargetRule_Model_Resource_Rule
     */
    protected function _prepareRuleProducts($object)
    {
        $this->unbindRuleFromEntity($object->getId(), array(), 'product');
        $this->bindRuleToEntity($object->getId(), $object->getMatchingProductIds(), 'product');

        return $this;
    }

    /**
     * Save Customer Segment Relations
     *
     * @deprecated after 1.11.2.0
     *
     * @param Mage_Core_Model_Abstract|Enterprise_TargetRule_Model_Rule $object
     *
     * @return Enterprise_TargetRule_Model_Resource_Rule
     */
    protected function _saveCustomerSegmentRelations(Mage_Core_Model_Abstract $object)
    {
        return $this;
    }

    /**
     * Retrieve target rule and customer segment relations table name
     *
     * @deprecated after 1.11.2.0
     *
     * @return string
     */
    protected function _getCustomerSegmentRelationsTable()
    {
        return '';
    }

    /**
     * Retrieve customer segment relations by target rule id
     *
     * @deprecated after 1.11.2.0
     *
     * @param int $ruleId
     *
     * @return array
     */
    public function getCustomerSegmentRelations($ruleId)
    {
        return array();
    }

    /**
     * Add Customer segment relations to Rule Resource Collection
     *
     * @deprecated after 1.11.2.0
     *
     * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection
     *
     * @return Enterprise_TargetRule_Model_Resource_Rule
     */
    public function addCustomerSegmentRelationsToCollection(Mage_Core_Model_Mysql4_Collection_Abstract $collection)
    {
        return $this;
    }

    /**
     * Retrieve target rule matched by condition products table name
     *
     * @deprecated after 1.11.2.0
     *
     * @return string
     */
    protected function _getRuleProductsTable()
    {
        return $this->getTable('enterprise_targetrule_product');
    }
}
