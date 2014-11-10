<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Model\Segment\Condition\Product\Combine;

use Magento\Customer\Model\Customer;
use Zend_Db_Expr;

/**
 * Last viewed/orderd items conditions combine
 */
class History extends \Magento\CustomerSegment\Model\Condition\Combine\AbstractCombine
{
    /**
     * Flag of using condition combine (for conditions of Product_Attribute)
     *
     * @var bool
     */
    protected $_combineProductCondition = true;

    const VIEWED = 'viewed_history';

    const ORDERED = 'ordered_history';

    /**
     * @var string
     */
    protected $_inputType = 'select';

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
        array $data = array()
    ) {
        parent::__construct($context, $conditionFactory, $resourceSegment, $data);
        $this->setType('Magento\CustomerSegment\Model\Segment\Condition\Product\Combine\History');
        $this->setValue(self::VIEWED);
    }

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return string[]
     */
    public function getMatchedEvents()
    {
        switch ($this->getValue()) {
            case self::ORDERED:
                $events = array('sales_order_save_commit_after');
                break;
            default:
                $events = array('catalog_controller_product_view');
        }
        return $events;
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return $this->_conditionFactory->create(
            'Product\Combine'
        )->setDateConditions(
            true
        )->getNewChildSelectOptions();
    }

    /**
     * Initialize value select options
     *
     * @return $this
     */
    public function loadValueOptions()
    {
        $this->setValueOption(array(self::VIEWED => __('viewed'), self::ORDERED => __('ordered')));
        return $this;
    }

    /**
     * Set rule instance
     *
     * Modify value_option array if needed
     *
     * @param \Magento\Rule\Model\Rule $rule
     * @return $this
     */
    public function setRule($rule)
    {
        $this->setData('rule', $rule);
        if ($rule instanceof \Magento\CustomerSegment\Model\Segment && $rule->getApplyTo() !== null) {
            $option = $this->loadValueOptions()->getValueOption();
            $applyTo = $rule->getApplyTo();
            if (\Magento\CustomerSegment\Model\Segment::APPLY_TO_VISITORS == $applyTo) {
                unset($option[self::ORDERED]);
            } elseif (\Magento\CustomerSegment\Model\Segment::APPLY_TO_VISITORS_AND_REGISTERED == $applyTo) {
                $option[self::VIEWED] .= '*';
            }
            $this->setValueOption($option);
        }
        return $this;
    }

    /**
     * Get input type for attribute value.
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'select';
    }

    /**
     * Prepare operator select options
     *
     * @return $this
     */
    public function loadOperatorOptions()
    {
        parent::loadOperatorOptions();
        $this->setOperatorOption(array('==' => __('was'), '!=' => __('was not')));
        return $this;
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml() . __(
            'If Product %1 %2 and matches %3 of these Conditions:',
            $this->getOperatorElementHtml(),
            $this->getValueElementHtml(),
            $this->getAggregatorElement()->getHtml()
        ) . $this->getRemoveLinkHtml();
    }

    /**
     * Build query for matching last viewed/orderd items
     *
     * @param Customer|Zend_Db_Expr $customer
     * @param int|Zend_Db_Expr $website
     * @return \Magento\Framework\DB\Select
     */
    protected function _prepareConditionsSql($customer, $website)
    {
        $select = $this->getResource()->createSelect();

        switch ($this->getValue()) {
            case self::ORDERED:
                $select->from(
                    array('item' => $this->getResource()->getTable('sales_order_item')),
                    array(new \Zend_Db_Expr(1))
                );
                $select->joinInner(
                    array('sales_order' => $this->getResource()->getTable('sales_order')),
                    'item.order_id = sales_order.entity_id',
                    array()
                );
                $select->where($this->_createCustomerFilter($customer, 'sales_order.customer_id'));
                $this->_limitByStoreWebsite($select, $website, 'sales_order.store_id');
                break;
            default:
                $select->from(
                    array('item' => $this->getResource()->getTable('report_viewed_product_index')),
                    array(new \Zend_Db_Expr(1))
                );
                if ($customer) {
                    // Leave ability to check this condition not only by customer_id but also by quote_id
                    $select->where('item.customer_id = :customer_id OR item.visitor_id = :visitor_id');
                } else {
                    $select->where($this->_createCustomerFilter($customer, 'item.customer_id'));
                }
                $this->_limitByStoreWebsite($select, $website, 'item.store_id');
                break;
        }
        $select->limit(1);
        return $select;
    }

    /**
     * Check if validation should be strict
     *
     * @return bool
     */
    protected function _getRequiredValidation()
    {
        return $this->getOperator() == '==';
    }

    /**
     * Get field names map for subfilter conditions
     *
     * @return array
     */
    protected function _getSubfilterMap()
    {
        switch ($this->getValue()) {
            case self::ORDERED:
                $dateField = 'item.created_at';
                break;

            default:
                $dateField = 'item.added_at';
                break;
        }

        return array('product' => 'item.product_id', 'date' => $dateField);
    }
}
