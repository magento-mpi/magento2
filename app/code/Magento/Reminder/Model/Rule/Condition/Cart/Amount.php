<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reminder\Model\Rule\Condition\Cart;

use Magento\Framework\Model\Exception;
use Magento\Framework\DB\Select;

/**
 * Cart totals amount condition
 */
class Amount extends \Magento\Reminder\Model\Condition\AbstractCondition
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
        array $data = array()
    ) {
        parent::__construct($context, $ruleResource, $data);
        $this->setType('Magento\Reminder\Model\Rule\Condition\Cart\Amount');
        $this->setValue(null);
    }

    /**
     * Get information for being presented in condition list
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return array('value' => $this->getType(), 'label' => __('Total Amount'));
    }

    /**
     * Init available options list
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption(array('subtotal' => __('subtotal'), 'grand_total' => __('grand total')));
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
            'Shopping cart %1 amount %2 %3:',
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
     * @throws Exception
     */
    public function getConditionsSql($customer, $website)
    {
        $table = $this->getResource()->getTable('sales_quote');
        $operator = $this->getResource()->getSqlOperator($this->getOperator());

        $select = $this->getResource()->createSelect();
        $select->from(array('quote' => $table), array(new \Zend_Db_Expr(1)));

        switch ($this->getAttribute()) {
            case 'subtotal':
                $field = 'quote.base_subtotal';
                break;
            case 'grand_total':
                $field = 'quote.base_grand_total';
                break;
            default:
                throw new Exception(__('Unknown quote total specified'));
        }

        $this->_limitByStoreWebsite($select, $website, 'quote.store_id');
        $select->where('quote.is_active = 1');
        $select->where("{$field} {$operator} ?", $this->getValue());
        $select->where($this->_createCustomerFilter($customer, 'customer_id'));
        $select->limit(1);
        return $select;
    }
}
