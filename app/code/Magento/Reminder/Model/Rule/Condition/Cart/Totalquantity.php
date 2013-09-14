<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cart product qty condition
 */
namespace Magento\Reminder\Model\Rule\Condition\Cart;

class Totalquantity
    extends \Magento\Reminder\Model\Condition\AbstractCondition
{
    /**
     * @var string
     */
    protected $_inputType = 'numeric';

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Rule\Model\Condition\Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('Magento\Reminder\Model\Rule\Condition\Cart\Totalquantity');
        $this->setValue(null);
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return array('value' => $this->getType(),
            'label' => __('Items Quantity'));
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . __('Total shopping cart items quantity %1 %2:', $this->getOperatorElementHtml(), $this->getValueElementHtml())
            . $this->getRemoveLinkHtml();
    }

    /**
     * Get SQL select for matching shopping cart products count
     *
     * @param $customer
     * @param int|Zend_Db_Expr $website
     * @return \Magento\DB\Select
     */
    public function getConditionsSql($customer, $website)
    {
        $table = $this->getResource()->getTable('sales_flat_quote');
        $operator = $this->getResource()->getSqlOperator($this->getOperator());

        $select = $this->getResource()->createSelect();
        $select->from(array('quote' => $table), array(new \Zend_Db_Expr(1)));

        $this->_limitByStoreWebsite($select, $website, 'quote.store_id');
        $select->where('quote.is_active = 1');
        $select->where("quote.items_qty {$operator} ?", $this->getValue());
        $select->where($this->_createCustomerFilter($customer, 'quote.customer_id'));
        $select->limit(1);

        return $select;
    }
}
