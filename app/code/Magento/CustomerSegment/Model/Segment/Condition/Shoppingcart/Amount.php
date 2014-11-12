<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Model\Segment\Condition\Shoppingcart;

use Magento\Customer\Model\Customer;
use Magento\CustomerSegment\Model\Condition\AbstractCondition;

/**
 * Shopping cart totals amount condition
 */
class Amount extends AbstractCondition
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
        array $data = array()
    ) {
        parent::__construct($context, $resourceSegment, $data);
        $this->setType('Magento\CustomerSegment\Model\Segment\Condition\Shoppingcart\Amount');
        $this->setValue(null);
    }

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return string[]
     */
    public function getMatchedEvents()
    {
        return array('sales_quote_save_commit_after');
    }

    /**
     * Get information for being presented in condition list
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return array(
            'value' => $this->getType(),
            'label' => __('Shopping Cart Total'),
            'available_in_guest_mode' => true
        );
    }

    /**
     * Init available options list
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption(
            array(
                'subtotal' => __('Subtotal'),
                'grand_total' => __('Grand Total'),
                'tax' => __('Tax'),
                'shipping' => __('Shipping'),
                'store_credit' => __('Store Credit'),
                'gift_card' => __('Gift Card')
            )
        );
        return $this;
    }

    /**
     * Set rule instance
     *
     * Modify attribute_option array if needed
     *
     * @param \Magento\Rule\Model\Rule $rule
     * @return $this
     */
    public function setRule($rule)
    {
        $this->setData('rule', $rule);
        if ($rule instanceof \Magento\CustomerSegment\Model\Segment && $rule->getApplyTo() !== null) {
            $attributeOption = $this->loadAttributeOptions()->getAttributeOption();
            $applyTo = $rule->getApplyTo();
            if (\Magento\CustomerSegment\Model\Segment::APPLY_TO_VISITORS == $applyTo) {
                unset($attributeOption['store_credit']);
            } elseif (\Magento\CustomerSegment\Model\Segment::APPLY_TO_VISITORS_AND_REGISTERED == $applyTo) {
                foreach (array_keys($attributeOption) as $key) {
                    if ('store_credit' != $key) {
                        $attributeOption[$key] .= '*';
                    }
                }
            }
            $this->setAttributeOption($attributeOption);
        }
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
            'Shopping Cart %1 Amount %2 %3:',
            $this->getAttributeElementHtml(),
            $this->getOperatorElementHtml(),
            $this->getValueElementHtml()
        ) . $this->getRemoveLinkHtml();
    }

    /**
     * Build condition limitations sql string for specific website
     *
     * @param Customer|\Zend_Db_Expr $customer
     * @param int|\Zend_Db_Expr $website
     * @throws \Magento\Framework\Model\Exception
     * @return \Magento\Framework\DB\Select
     */
    public function getConditionsSql($customer, $website)
    {
        $table = $this->getResource()->getTable('sales_quote');
        $addressTable = $this->getResource()->getTable('sales_quote_address');
        $operator = $this->getResource()->getSqlOperator($this->getOperator());

        $select = $this->getResource()->createSelect();
        $select->from(array('quote' => $table), array(new \Zend_Db_Expr(1)))->where('quote.is_active=1');
        $select->limit(1);
        $this->_limitByStoreWebsite($select, $website, 'quote.store_id');

        $joinAddress = false;
        switch ($this->getAttribute()) {
            case 'subtotal':
                $field = 'quote.base_subtotal';
                break;
            case 'grand_total':
                $field = 'quote.base_grand_total';
                break;
            case 'tax':
                $field = 'base_tax_amount';
                $joinAddress = true;
                break;
            case 'shipping':
                $field = 'base_shipping_amount';
                $joinAddress = true;
                break;
            case 'store_credit':
                $field = 'quote.base_customer_bal_amount_used';
                break;
            case 'gift_card':
                $field = 'quote.base_gift_cards_amount_used';
                break;
            default:
                throw new \Magento\Framework\Model\Exception(__('Unknown quote total specified.'));
        }

        if ($joinAddress) {
            $subSelect = $this->getResource()->createSelect();
            $subSelect->from(
                array('address' => $addressTable),
                array('quote_id' => 'quote_id', $field => new \Zend_Db_Expr("SUM({$field})"))
            );

            $subSelect->group('quote_id');
            $select->joinInner(array('address' => $subSelect), 'address.quote_id = quote.entity_id', array());
            $field = "address.{$field}";
        }

        $select->where("{$field} {$operator} ?", $this->getValue());
        if ($customer) {
            // Leave ability to check this condition not only by customer_id but also by quote_id
            $select->where('quote.customer_id = :customer_id OR quote.entity_id = :quote_id');
        } else {
            $select->where($this->_createCustomerFilter($customer, 'quote.customer_id'));
        }
        return $select;
    }
}
