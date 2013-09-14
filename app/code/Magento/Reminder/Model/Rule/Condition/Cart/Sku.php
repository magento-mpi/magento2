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
 * Cart items SKU subselection condition
 */
namespace Magento\Reminder\Model\Rule\Condition\Cart;

class Sku
    extends \Magento\Reminder\Model\Condition\AbstractCondition
{
    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Rule\Model\Condition\Context $context, array $data = array())
    {
        parent::__construct($context, $data);
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
        return array('value' => $this->getType(),
            'label' => __('SKU'));
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . __('Item SKU %1 %2 ', $this->getOperatorElementHtml(), $this->getValueElementHtml())
            . $this->getRemoveLinkHtml();
    }

    /**
     * Initialize value select options
     *
     * @return Magento_Reminder_Model_Rule_Condition_Wishlist_Sku
     */
    public function loadValueOptions()
    {
        $this->setValueOption(\Mage::getSingleton('Magento\Core\Model\System\Store')->getStoreOptionHash());
        return $this;
    }

    /**
     * Get SQL select
     *
     * @param $customer
     * @param int | \Zend_Db_Expr $website
     * @return \Magento\DB\Select
     */
    public function getConditionsSql($customer, $website)
    {
        $quoteTable = $this->getResource()->getTable('sales_flat_quote');
        $quoteItemTable = $this->getResource()->getTable('sales_flat_quote_item');
        $operator = $this->getResource()->getSqlOperator($this->getOperator());

        $select = $this->getResource()->createSelect();
        $select->from(array('item' => $quoteItemTable), array(new \Zend_Db_Expr(1)));

        $select->joinInner(
            array('quote' => $quoteTable),
            'item.quote_id = quote.entity_id',
            array()
        );

        $this->_limitByStoreWebsite($select, $website, 'quote.store_id');
        $select->where('quote.is_active = 1');
        $select->where("item.sku {$operator} ?", $this->getValue());
        $select->where($this->_createCustomerFilter($customer, 'quote.customer_id'));
        $select->limit(1);

        return $select;
    }
}
