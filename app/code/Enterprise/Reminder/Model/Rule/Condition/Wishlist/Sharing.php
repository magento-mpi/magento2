<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist sharing condition
 */
class Enterprise_Reminder_Model_Rule_Condition_Wishlist_Sharing
    extends Enterprise_Reminder_Model_Condition_Abstract
{
    /**
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(Magento_Rule_Model_Condition_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('Enterprise_Reminder_Model_Rule_Condition_Wishlist_Sharing');
        $this->setValue(1);
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return array('value' => $this->getType(),
            'label' => __('Sharing'));
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . __('Wish List %1 shared', $this->getValueElementHtml())
            . $this->getRemoveLinkHtml();
    }

    /**
     * Get element type for value select
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'select';
    }

    /**
     * Init list of available values
     *
     * @return Enterprise_Reminder_Model_Rule_Condition_Wishlist_Sharing
     */
    public function loadValueOptions()
    {
        $this->setValueOption(array(
            '1' => __('is'),
            '0' => __('is not'),
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
        $table = $this->getResource()->getTable('wishlist');

        $select = $this->getResource()->createSelect();
        $select->from(array('list' => $table), array(new Zend_Db_Expr(1)));
        if ($this->getValue()) {
            $select->where("list.shared > 0");
        } else {
            $select->where("list.shared = 0");
        }
        $select->where($this->_createCustomerFilter($customer, 'list.customer_id'));
        $select->limit(1);

        return $select;
    }
}
