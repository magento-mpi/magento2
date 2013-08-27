<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_SalesRule_Model_Rule_Condition_Combine extends Magento_Rule_Model_Condition_Combine
{
    /**
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(Magento_Rule_Model_Condition_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('Magento_SalesRule_Model_Rule_Condition_Combine');
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $addressCondition = Mage::getModel('Magento_SalesRule_Model_Rule_Condition_Address');
        $addressAttributes = $addressCondition->loadAttributeOptions()->getAttributeOption();
        $attributes = array();
        foreach ($addressAttributes as $code=>$label) {
            $attributes[] = array(
                'value' => 'Magento_SalesRule_Model_Rule_Condition_Address|' . $code, 'label' => $label
            );
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('value' => 'Magento_SalesRule_Model_Rule_Condition_Product_Found',
                'label' => __('Product attribute combination')
            ),
            array('value' => 'Magento_SalesRule_Model_Rule_Condition_Product_Subselect',
                'label' => __('Products subselection')
            ),
            array('value' => 'Magento_SalesRule_Model_Rule_Condition_Combine',
                'label' => __('Conditions combination')
            ),
            array('label' => __('Cart Attribute'), 'value' => $attributes),
        ));

        $additional = new Magento_Object();
        Mage::dispatchEvent('salesrule_rule_condition_combine', array('additional' => $additional));
        $additionalConditions = $additional->getConditions();
        if ($additionalConditions) {
            $conditions = array_merge_recursive($conditions, $additionalConditions);
        }

        return $conditions;
    }
}
