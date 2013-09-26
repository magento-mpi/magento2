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
 * Catalog Rule Combine Condition data model
 */
class Magento_CatalogRule_Model_Rule_Condition_Combine extends Magento_Rule_Model_Condition_Combine
{
    /**
     * @var Magento_CatalogRule_Model_Rule_Condition_ProductFactory
     */
    protected $_conditionFactory;

    /**
     * @param Magento_CatalogRule_Model_Rule_Condition_ProductFactory $conditionFactory
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_CatalogRule_Model_Rule_Condition_ProductFactory $conditionFactory,
        Magento_Rule_Model_Condition_Context $context,
        array $data = array()
    ) {
        $this->_conditionFactory = $conditionFactory;
        parent::__construct($context, $data);
        $this->setType('Magento_CatalogRule_Model_Rule_Condition_Combine');
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $productAttributes = $this->_conditionFactory->create()
            ->loadAttributeOptions()
            ->getAttributeOption();
        $attributes = array();
        foreach ($productAttributes as $code => $label) {
            $attributes[] = array(
                'value' => 'Magento_CatalogRule_Model_Rule_Condition_Product|' . $code, 'label' => $label
            );
        }
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array(
                'value' => 'Magento_CatalogRule_Model_Rule_Condition_Combine',
                'label' => __('Conditions Combination')
            ),
            array(
                'label' => __('Product Attribute'),
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
