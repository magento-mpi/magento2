<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerSegment\Model\Segment\Condition\Product;

use Magento\Customer\Model\Customer;
use Zend_Db_Expr;

/**
 * Product attributes condition combine
 */
class Combine extends \Magento\CustomerSegment\Model\Condition\Combine\AbstractCombine
{
    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory
     * @param \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory,
        \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment,
        array $data = []
    ) {
        parent::__construct($context, $conditionFactory, $resourceSegment, $data);
        $this->setType('Magento\CustomerSegment\Model\Segment\Condition\Product\Combine');
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $children = array_merge_recursive(
            parent::getNewChildSelectOptions(),
            [['value' => $this->getType(), 'label' => __('Conditions Combination')]]
        );
        if ($this->getDateConditions()) {
            $children = array_merge_recursive(
                $children,
                [
                    [
                        'value' => [
                            $this->_conditionFactory->create('Uptodate')->getNewChildSelectOptions(),
                            $this->_conditionFactory->create('Daterange')->getNewChildSelectOptions(),
                        ],
                        'label' => __('Date Ranges'),
                    ]
                ]
            );
        }
        $children = array_merge_recursive(
            $children,
            [$this->_conditionFactory->create('Product\Attributes')->getNewChildSelectOptions()]
        );
        return $children;
    }

    /**
     * Combine not present his own SQL condition
     *
     * @param Customer|Zend_Db_Expr $customer
     * @param int|Zend_Db_Expr $website
     * @return false
     */
    public function getConditionsSql($customer, $website)
    {
        return false;
    }

    /**
     * Get combine subfilter type
     *
     * @return string
     */
    public function getSubfilterType()
    {
        return 'product';
    }

    /**
     * Apply product attribute subfilter to parent/base condition query
     *
     * @param string $fieldName base query field name
     * @param bool $requireValid strict validation flag
     * @param int|Zend_Db_Expr $website
     * @return string
     */
    public function getSubfilterSql($fieldName, $requireValid, $website)
    {
        $table = $this->getResource()->getTable('catalog_product_entity');

        $select = $this->getResource()->createSelect();
        $select->from(['main' => $table], ['entity_id']);

        if ($this->getAggregator() == 'all') {
            $whereFunction = 'where';
        } else {
            $whereFunction = 'orWhere';
        }

        $gotConditions = false;
        foreach ($this->getConditions() as $condition) {
            if ($condition->getSubfilterType() == 'product') {
                $subfilter = $condition->getSubfilterSql('main.entity_id', $this->getValue() == 1, $website);
                if ($subfilter) {
                    $select->{$whereFunction}($subfilter);
                    $gotConditions = true;
                }
            }
        }
        if (!$gotConditions) {
            $select->where('1=1');
        }

        $inOperator = $requireValid ? 'IN' : 'NOT IN';
        return sprintf("%s %s (%s)", $fieldName, $inOperator, $select);
    }
}
