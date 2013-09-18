<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_TargetRule_Model_Actions_Condition_Combine extends Magento_Rule_Model_Condition_Combine
{
    /**
     * @var Magento_TargetRule_Model_Actions_Condition_Product_AttributesFactory
     */
    protected $_attributeFactory;

    /**
     * @var Magento_TargetRule_Model_Actions_Condition_Product_Special
     */
    protected $_specialFactory;

    /**
     * @param Magento_TargetRule_Model_Actions_Condition_Product_AttributesFactory $attributeFactory
     * @param Magento_TargetRule_Model_Actions_Condition_Product_Special $specialFactory
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_TargetRule_Model_Actions_Condition_Product_AttributesFactory $attributeFactory,
        Magento_TargetRule_Model_Actions_Condition_Product_Special $specialFactory,
        Magento_Rule_Model_Condition_Context $context,
        array $data = array()
    ) {
        $this->_attributeFactory = $attributeFactory;
        $this->_specialFactory = $specialFactory;
        parent::__construct($context, $data);
        $this->setType('Magento_TargetRule_Model_Actions_Condition_Combine');
    }

    /**
     * Prepare list of contitions
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = array(
            array('value'=>$this->getType(), 'label'=>__('Conditions Combination')),
            $this->_attributeFactory->create()->getNewChildSelectOptions(),
            $this->_specialFactory->create()->getNewChildSelectOptions(),
        );
        $conditions = array_merge_recursive(parent::getNewChildSelectOptions(), $conditions);
        return $conditions;
    }

    /**
     * Retrieve SELECT WHERE condition for product collection
     *
     * @param Magento_Catalog_Model_Resource_Product_Collection $collection
     * @param Magento_TargetRule_Model_Index $object
     * @param array $bind
     * @return Zend_Db_Expr
     */
    public function getConditionForCollection($collection, $object, &$bind)
    {
        $conditions = array();
        $aggregator = $this->getAggregator() == 'all' ? ' AND ' : ' OR ';
        $operator   = $this->getValue() ? '' : 'NOT';

        foreach ($this->getConditions() as $condition) {
            $subCondition = $condition->getConditionForCollection($collection, $object, $bind);
            if ($subCondition) {
                $conditions[] = sprintf('%s %s', $operator, $subCondition);
            }
        }

        if ($conditions) {
            return new Zend_Db_Expr(sprintf('(%s)', join($aggregator, $conditions)));
        }

        return false;
    }
}

