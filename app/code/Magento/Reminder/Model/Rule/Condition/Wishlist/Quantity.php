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
 * Wishlist items quantity condition
 */
class Magento_Reminder_Model_Rule_Condition_Wishlist_Quantity
    extends Magento_Reminder_Model_Condition_Abstract
{
    /**
     * @var string
     */
    protected $_inputType = 'numeric';

    /**
     * @param Magento_Rule_Model_Condition_Context $context
     * @param Magento_Reminder_Model_Resource_Rule $ruleResource
     * @param array $data
     */
    public function __construct(
        Magento_Rule_Model_Condition_Context $context,
        Magento_Reminder_Model_Resource_Rule $ruleResource,
        array $data = array()
    ) {
        parent::__construct($context, $ruleResource, $data);
        $this->setType('Magento_Reminder_Model_Rule_Condition_Wishlist_Quantity');
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
            'label' => __('Number of Items'));
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . __('Number of wish list items %1 %2 ', $this->getOperatorElementHtml(), $this->getValueElementHtml())
            . $this->getRemoveLinkHtml();
    }

    /**
     * Get SQL select
     *
     * @param $customer
     * @param int | Zend_Db_Expr $website
     * @return Magento_DB_Select
     */
    public function getConditionsSql($customer, $website)
    {
        $wishlistTable = $this->getResource()->getTable('wishlist');
        $wishlistItemTable = $this->getResource()->getTable('wishlist_item');
        $operator = $this->getResource()->getSqlOperator($this->getOperator());
        $value = (int) $this->getValue();
        $result = $this->getResource()->getReadConnection()->getCheckSql(
            "COUNT(*) {$operator} {$value}",
            1,
            0
        );

        $select = $this->getResource()->createSelect();
        $select->from(array('item' => $wishlistItemTable), array(new Zend_Db_Expr($result)));

        $select->joinInner(
            array('list' => $wishlistTable),
            'item.wishlist_id = list.wishlist_id',
            array()
        );

        $this->_limitByStoreWebsite($select, $website, 'item.store_id');
        $select->where($this->_createCustomerFilter($customer, 'list.customer_id'));
        return $select;
    }
}
