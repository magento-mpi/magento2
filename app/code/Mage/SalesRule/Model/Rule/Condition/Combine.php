<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_SalesRule_Model_Rule_Condition_Combine extends Mage_Rule_Model_Condition_Combine
{
    public function __construct(Mage_Rule_Model_Condition_Context $context)
    {
        parent::__construct($context);
        $this->setType('Mage_SalesRule_Model_Rule_Condition_Combine');
    }

    public function getNewChildSelectOptions()
    {
        $addressCondition = Mage::getModel('Mage_SalesRule_Model_Rule_Condition_Address');
        $addressAttributes = $addressCondition->loadAttributeOptions()->getAttributeOption();
        $attributes = array();
        foreach ($addressAttributes as $code=>$label) {
            $attributes[] = array(
                'value' => 'Mage_SalesRule_Model_Rule_Condition_Address|' . $code, 'label' => $label
            );
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('value' => 'Mage_SalesRule_Model_Rule_Condition_Product_Found',
                'label' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Product attribute combination')
            ),
            array('value' => 'Mage_SalesRule_Model_Rule_Condition_Product_Subselect',
                'label' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Products subselection')
            ),
            array('value' => 'Mage_SalesRule_Model_Rule_Condition_Combine',
                'label' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Conditions combination')
            ),
            array('label' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Cart Attribute'), 'value' => $attributes),
        ));

        $additional = new Varien_Object();
        Mage::dispatchEvent('salesrule_rule_condition_combine', array('additional' => $additional));
        if ($additionalConditions = $additional->getConditions()) {
            $conditions = array_merge_recursive($conditions, $additionalConditions);
        }

        return $conditions;
    }
}
