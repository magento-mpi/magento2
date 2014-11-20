<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reminder\Model\Rule\Condition\Cart;

use Magento\Framework\DB\Select;

/**
 * Cart items SKU sub-selection condition
 */
class Sku extends \Magento\Reminder\Model\Condition\AbstractCondition
{
    /**
     * Store
     *
     * @var \Magento\Store\Model\System\Store
     */
    protected $_store;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Reminder\Model\Resource\Rule $ruleResource
     * @param \Magento\Store\Model\System\Store $store
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Reminder\Model\Resource\Rule $ruleResource,
        \Magento\Store\Model\System\Store $store,
        array $data = array()
    ) {
        $this->_store = $store;
        parent::__construct($context, $ruleResource, $data);
        $this->setType('Magento\Reminder\Model\Rule\Condition\Cart\Sku');
        $this->setValue(null);
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return array('value' => $this->getType(), 'label' => __('SKU'));
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml() . __(
            'Item SKU %1 %2 ',
            $this->getOperatorElementHtml(),
            $this->getValueElementHtml()
        ) . $this->getRemoveLinkHtml();
    }

    /**
     * Initialize value select options
     *
     * @return $this
     */
    public function loadValueOptions()
    {
        $this->setValueOption($this->_store->getStoreOptionHash());
        return $this;
    }

    /**
     * Get SQL select
     *
     * @param null|int|\Zend_Db_Expr $customer
     * @param int|\Zend_Db_Expr $website
     * @return Select
     */
    public function getConditionsSql($customer, $website)
    {
        $quoteTable = $this->getResource()->getTable('sales_quote');
        $quoteItemTable = $this->getResource()->getTable('sales_quote_item');
        $operator = $this->getResource()->getSqlOperator($this->getOperator());

        $select = $this->getResource()->createSelect();
        $select->from(array('item' => $quoteItemTable), array(new \Zend_Db_Expr(1)));

        $select->joinInner(array('quote' => $quoteTable), 'item.quote_id = quote.entity_id', array());

        $this->_limitByStoreWebsite($select, $website, 'quote.store_id');
        $select->where('quote.is_active = 1');
        $select->where("item.sku {$operator} ?", $this->getValue());
        $select->where($this->_createCustomerFilter($customer, 'quote.customer_id'));
        $select->limit(1);

        return $select;
    }
}
