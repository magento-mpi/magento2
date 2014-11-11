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
 * Shopping cart/wishlist items condition
 */
class ListCombine extends \Magento\CustomerSegment\Model\Condition\Combine\AbstractCombine
{
    /**
     * Flag of using condition combine (for conditions of Product_Attribute)
     *
     * @var bool
     */
    protected $_combineProductCondition = true;

    const WISHLIST = 'wishlist';

    const CART = 'shopping_cart';

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
        $this->setType('Magento\CustomerSegment\Model\Segment\Condition\Product\Combine\ListCombine');
        $this->setValue(self::CART);
    }

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return string[]
     */
    public function getMatchedEvents()
    {
        $events = array();
        switch ($this->getValue()) {
            case self::WISHLIST:
                $events = array('wishlist_items_renewed');
                break;
            default:
                $events = array('checkout_cart_save_after');
                break;
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
        $this->setValueOption(array(self::CART => __('Shopping Cart'), self::WISHLIST => __('Wish List')));
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
                unset($option[self::WISHLIST]);
            } elseif (\Magento\CustomerSegment\Model\Segment::APPLY_TO_VISITORS_AND_REGISTERED == $applyTo) {
                $option[self::CART] .= '*';
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
        $this->setOperatorOption(array('==' => __('found'), '!=' => __('not found')));
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
            'If Product is %1 in the %2 with %3 of these Conditions match:',
            $this->getOperatorElementHtml(),
            $this->getValueElementHtml(),
            $this->getAggregatorElement()->getHtml()
        ) . $this->getRemoveLinkHtml();
    }

    /**
     * Build query for matching shopping cart/wishlist items
     *
     * @param Customer|Zend_Db_Expr $customer
     * @param int|Zend_Db_Expr $website
     * @return \Magento\Framework\DB\Select
     */
    protected function _prepareConditionsSql($customer, $website)
    {
        $select = $this->getResource()->createSelect();

        switch ($this->getValue()) {
            case self::WISHLIST:
                $select->from(
                    array('item' => $this->getResource()->getTable('wishlist_item')),
                    array(new \Zend_Db_Expr(1))
                );
                $conditions = "item.wishlist_id = list.wishlist_id";
                $select->joinInner(array('list' => $this->getResource()->getTable('wishlist')), $conditions, array());
                $this->_limitByStoreWebsite($select, $website, 'item.store_id');
                $select->where($this->_createCustomerFilter($customer, 'list.customer_id'));
                break;
            default:
                $select->from(
                    array('item' => $this->getResource()->getTable('sales_quote_item')),
                    array(new \Zend_Db_Expr(1))
                );
                $conditions = "item.quote_id = list.entity_id";
                $select->joinInner(
                    array('list' => $this->getResource()->getTable('sales_quote')),
                    $conditions,
                    array()
                );
                $this->_limitByStoreWebsite($select, $website, 'list.store_id');
                $select->where('list.is_active = ?', new \Zend_Db_Expr(1));
                if ($customer) {
                    // Leave ability to check this condition not only by customer_id but also by quote_id
                    $select->where('list.customer_id = :customer_id OR list.entity_id = :quote_id');
                } else {
                    $select->where($this->_createCustomerFilter($customer, 'list.customer_id'));
                }
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
            case self::WISHLIST:
                $dateField = 'item.added_at';
                break;

            default:
                $dateField = 'item.created_at';
                break;
        }

        return array('product' => 'item.product_id', 'date' => $dateField);
    }
}
