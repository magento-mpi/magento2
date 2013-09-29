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
     * @var \Magento\Core\Model\Event\Manager
     */
    protected $_eventManager = null;

    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\Address
     */
    protected $_conditionAddress;

    /**
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\SalesRule\Model\Rule\Condition\Address $conditionAddress
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\SalesRule\Model\Rule\Condition\Address $conditionAddress,
        array $data = array()
    ) {
        $this->_eventManager = $eventManager;
        $this->_conditionAddress = $conditionAddress;
        parent::__construct($context, $data);
        $this->setType('Magento\SalesRule\Model\Rule\Condition\Combine');
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $addressAttributes = $this->_conditionAddress->loadAttributeOptions()->getAttributeOption();
        $attributes = array();
        foreach ($addressAttributes as $code=>$label) {
            $attributes[] = array(
                'value' => 'Magento_SalesRule_Model_Rule_Condition_Address|' . $code, 'label' => $label
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
