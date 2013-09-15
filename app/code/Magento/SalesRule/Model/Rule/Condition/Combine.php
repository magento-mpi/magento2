<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\SalesRule\Model\Rule\Condition;

class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager = null;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Rule_Model_Condition_Context $context,
        array $data = array()
    ) {
        $this->_eventManager = $eventManager;
        parent::__construct($context, $data);
        $this->setType('Magento\SalesRule\Model\Rule\Condition\Combine');
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $addressCondition = \Mage::getModel('Magento\SalesRule\Model\Rule\Condition\Address');
        $addressAttributes = $addressCondition->loadAttributeOptions()->getAttributeOption();
        $attributes = array();
        foreach ($addressAttributes as $code=>$label) {
            $attributes[] = array(
                'value' => 'Magento\SalesRule\Model\Rule\Condition\Address|' . $code, 'label' => $label
            );
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('value' => 'Magento\SalesRule\Model\Rule\Condition\Product\Found',
                'label' => __('Product attribute combination')
            ),
            array('value' => 'Magento\SalesRule\Model\Rule\Condition\Product\Subselect',
                'label' => __('Products subselection')
            ),
            array('value' => 'Magento\SalesRule\Model\Rule\Condition\Combine',
                'label' => __('Conditions combination')
            ),
            array('label' => __('Cart Attribute'), 'value' => $attributes),
        ));

        $additional = new \Magento\Object();
        $this->_eventManager->dispatch('salesrule_rule_condition_combine', array('additional' => $additional));
        $additionalConditions = $additional->getConditions();
        if ($additionalConditions) {
            $conditions = array_merge_recursive($conditions, $additionalConditions);
        }

        return $conditions;
    }
}
