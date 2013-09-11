<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\TargetRule\Model\Actions\Condition;

class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * Set condition type
     *
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Rule\Model\Condition\Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('\Magento\TargetRule\Model\Actions\Condition\Combine');
    }

    /**
     * Prepare list of contitions
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = array(
            array('value'=>$this->getType(),
                'label'=>__('Conditions Combination')),
            \Mage::getModel('Magento\TargetRule\Model\Actions\Condition\Product\Attributes')
                ->getNewChildSelectOptions(),
            \Mage::getModel('Magento\TargetRule\Model\Actions\Condition\Product\Special')
                ->getNewChildSelectOptions(),
        );
        $conditions = array_merge_recursive(parent::getNewChildSelectOptions(), $conditions);
        return $conditions;
    }

    /**
     * Retrieve SELECT WHERE condition for product collection
     *
     * @param \Magento\Catalog\Model\Resource\Product\Collection $collection
     * @param \Magento\TargetRule\Model\Index $object
     * @param array $bind
     * @return \Zend_Db_Expr
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
            return new \Zend_Db_Expr(sprintf('(%s)', join($aggregator, $conditions)));
        }

        return false;
    }
}

