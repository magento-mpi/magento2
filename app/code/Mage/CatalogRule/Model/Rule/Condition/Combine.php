<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Rule Combine Condition data model
 */
class Mage_CatalogRule_Model_Rule_Condition_Combine extends Mage_Rule_Model_Condition_Combine
{
    public function __construct(Mage_Rule_Model_Condition_Context $context)
    {
        parent::__construct($context);
        $this->setType('Mage_CatalogRule_Model_Rule_Condition_Combine');
    }

    public function getNewChildSelectOptions()
    {
        $productCondition = Mage::getModel('Mage_CatalogRule_Model_Rule_Condition_Product');
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();
        $attributes = array();
        foreach ($productAttributes as $code => $label) {
            $attributes[] = array(
                'value' => 'Mage_CatalogRule_Model_Rule_Condition_Product|' . $code, 'label' => $label
            );
        }
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('value' => 'Mage_CatalogRule_Model_Rule_Condition_Combine',
                'label' => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Conditions Combination')
            ),
            array('label' => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Product Attribute'),
                'value' => $attributes
            ),
        ));
        return $conditions;
    }

    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }
}
