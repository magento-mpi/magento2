<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_TargetRule_Model_Rule_Condition_Combine extends Magento_Rule_Model_Condition_Combine
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
        $this->setType('Magento_TargetRule_Model_Rule_Condition_Combine');
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
     * @param Magento_Catalog_Model_Resource_Product_Collection $productCollection
     * @return Magento_TargetRule_Model_Rule_Condition_Combine
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }
}
