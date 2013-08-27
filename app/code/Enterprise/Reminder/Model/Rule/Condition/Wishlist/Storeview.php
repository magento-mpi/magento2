<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Enterprise_Reminder_Model_Rule_Condition_Wishlist_Storeview
    extends Enterprise_Reminder_Model_Condition_Abstract
{
    /**
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(Magento_Rule_Model_Condition_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('Enterprise_Reminder_Model_Rule_Condition_Wishlist_Storeview');
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
            'label' => __('Store View'));
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . __('The item was added to wish list %1, store view %2.', $this->getOperatorElementHtml(), $this->getValueElementHtml())
            . $this->getRemoveLinkHtml();
    }

    /**
     * Initialize value select options
     *
     * @return Enterprise_Reminder_Model_Rule_Condition_Wishlist_Storeview
     */
    public function loadValueOptions()
    {
        $this->setValueOption(Mage::getSingleton('Magento_Core_Model_System_Store')->getStoreValuesForForm());
        return $this;
    }

    /**
     * Get select options
     *
     * @return array
     */
    public function getValueSelectOptions()
    {
        return $this->getValueOption();
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
     * @return Enterprise_Reminder_Model_Rule_Condition_Wishlist_Storeview
     */
    public function loadOperatorOptions()
    {
        parent::loadOperatorOptions();
        $this->setOperatorOption(array(
            '==' => __('from'),
            '!=' => __('not from')
        ));
        return $this;
    }

    /**
     * Get SQL select
     *
     * @param $customer
     * @param int|Zend_Db_Expr $website
     * @return Magento_DB_Select
     */
    public function getConditionsSql($customer, $website)
    {
        $wishlistTable = $this->getResource()->getTable('wishlist');
        $wishlistItemTable = $this->getResource()->getTable('wishlist_item');
        $operator = $this->getResource()->getSqlOperator($this->getOperator());

        $select = $this->getResource()->createSelect();
        $select->from(array('item' => $wishlistItemTable), array(new Zend_Db_Expr(1)));

        $select->joinInner(
            array('list' => $wishlistTable),
            'item.wishlist_id = list.wishlist_id',
            array()
        );

        $this->_limitByStoreWebsite($select, $website, 'item.store_id');
        $select->where("item.store_id {$operator} ?", $this->getValue());
        $select->where($this->_createCustomerFilter($customer, 'list.customer_id'));
        $select->limit(1);

        return $select;
    }
}
