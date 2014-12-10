<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerSegment\Model\Segment\Condition\Shoppingcart;

use Magento\Customer\Model\Customer;
use Magento\CustomerSegment\Model\Condition\AbstractCondition;

/**
 * Shopping cart product qty condition
 */
class Productsquantity extends AbstractCondition
{
    /**
     * @var string
     */
    protected $_inputType = 'numeric';

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment,
        array $data = []
    ) {
        parent::__construct($context, $resourceSegment, $data);
        $this->setType('Magento\CustomerSegment\Model\Segment\Condition\Shoppingcart\Productsquantity');
        $this->setValue(null);
    }

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return string[]
     */
    public function getMatchedEvents()
    {
        return ['sales_quote_save_commit_after'];
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return [
            'value' => $this->getType(),
            'label' => __('Products Quantity'),
            'available_in_guest_mode' => true
        ];
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml() . __(
            'Shopping Cart Products Qty %1 %2:',
            $this->getOperatorElementHtml(),
            $this->getValueElementHtml()
        ) . $this->getRemoveLinkHtml();
    }

    /**
     * Get SQL select for matching shopping cart products count
     *
     * @param Customer|\Zend_Db_Expr $customer
     * @param int|\Zend_Db_Expr $website
     * @return \Magento\Framework\DB\Select
     */
    public function getConditionsSql($customer, $website)
    {
        $table = $this->getResource()->getTable('sales_quote');
        $operator = $this->getResource()->getSqlOperator($this->getOperator());

        $select = $this->getResource()->createSelect();
        $select->from(['quote' => $table], [new \Zend_Db_Expr(1)])->where('quote.is_active=1');
        $this->_limitByStoreWebsite($select, $website, 'quote.store_id');
        $select->limit(1);
        $select->where("quote.items_qty {$operator} ?", $this->getValue());
        if ($customer) {
            // Leave ability to check this condition not only by customer_id but also by quote_id
            $select->where('quote.customer_id = :customer_id OR quote.entity_id = :quote_id');
        } else {
            $select->where($this->_createCustomerFilter($customer, 'quote.customer_id'));
        }

        return $select;
    }
}
