<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reminder\Model\Rule\Condition\Cart;

use Magento\Framework\DB\Select;
use Magento\Framework\Model\Exception;

/**
 * Cart items attributes subselection condition
 */
class Attributes extends \Magento\Reminder\Model\Condition\AbstractCondition
{
    /**
     * @var string
     */
    protected $_inputType = 'numeric';

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Reminder\Model\Resource\Rule $ruleResource
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Reminder\Model\Resource\Rule $ruleResource,
        array $data = []
    ) {
        parent::__construct($context, $ruleResource, $data);
        $this->setType('Magento\Reminder\Model\Rule\Condition\Cart\Attributes');
        $this->setValue(null);
    }

    /**
     * Get information for being presented in condition list
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return ['value' => $this->getType(), 'label' => __('Numeric Attribute')];
    }

    /**
     * Init available options list
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption(
            [
                'weight' => __('weight'),
                'row_weight' => __('row weight'),
                'qty' => __('quantity'),
                'price' => __('base price'),
                'base_cost' => __('base cost'),
            ]
        );
        return $this;
    }

    /**
     * Condition string on conditions page
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml() . __(
            'Item %1 %2 %3:',
            $this->getAttributeElementHtml(),
            $this->getOperatorElementHtml(),
            $this->getValueElementHtml()
        ) . $this->getRemoveLinkHtml();
    }

    /**
     * Build condition limitations sql string for specific website
     *
     * @param null|int|\Zend_Db_Expr $customer
     * @param int|\Zend_Db_Expr $website
     * @return Select
     * @throws \Magento\Framework\Model\Exception
     */
    public function getConditionsSql($customer, $website)
    {
        $quoteTable = $this->getResource()->getTable('sales_quote');
        $quoteItemTable = $this->getResource()->getTable('sales_quote_item');
        $operator = $this->getResource()->getSqlOperator($this->getOperator());

        $select = $this->getResource()->createSelect();
        $select->from(['item' => $quoteItemTable], [new \Zend_Db_Expr(1)]);

        $select->joinInner(['quote' => $quoteTable], 'item.quote_id = quote.entity_id', []);

        switch ($this->getAttribute()) {
            case 'weight':
                $field = 'item.weight';
                break;
            case 'row_weight':
                $field = 'item.row_weight';
                break;
            case 'qty':
                $field = 'item.qty';
                break;
            case 'price':
                $field = 'item.price';
                break;
            case 'base_cost':
                $field = 'item.base_cost';
                break;
            default:
                throw new Exception(__('Unknown attribute specified'));
        }

        $this->_limitByStoreWebsite($select, $website, 'quote.store_id');
        $select->where('quote.is_active = 1');
        $select->where("{$field} {$operator} ?", $this->getValue());
        $select->where($this->_createCustomerFilter($customer, 'quote.customer_id'));
        $select->limit(1);
        return $select;
    }
}
