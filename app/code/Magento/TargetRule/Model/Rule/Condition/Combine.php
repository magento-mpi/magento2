<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\TargetRule\Model\Rule\Condition;

class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var Magento_TargetRule_Model_Rule_Condition_Product_AttributesFactory
     */
    protected $_attributeFactory;

    /**
     * @param Magento_TargetRule_Model_Rule_Condition_Product_AttributesFactory $attributesFactory
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_TargetRule_Model_Rule_Condition_Product_AttributesFactory $attributesFactory,
        Magento_Rule_Model_Condition_Context $context,
        array $data = array()
    ) {
        $this->_attributeFactory = $attributesFactory;
        parent::__construct($context, $data);
        $this->setType('Magento\TargetRule\Model\Rule\Condition\Combine');
    }

    /**
     * Prepare list of contitions
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = array(
            array(
                'value' => $this->getType(),
                'label' => __('Conditions Combination')
            ),
            $this->_attributeFactory->create()->getNewChildSelectOptions(),
        );

        $conditions = array_merge_recursive(parent::getNewChildSelectOptions(), $conditions);
        return $conditions;
    }

    /**
     * Collect validated attributes for Product Collection
     *
     * @param \Magento\Catalog\Model\Resource\Product\Collection $productCollection
     * @return \Magento\TargetRule\Model\Rule\Condition\Combine
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }
}
