<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reminder\Model\Rule\Condition\Wishlist;

use Magento\DB\Select;

/**
 * Wishlist sharing condition
 */
class Sharing
    extends \Magento\Reminder\Model\Condition\AbstractCondition
{
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
        $this->setType('Magento\Reminder\Model\Rule\Condition\Wishlist\Sharing');
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
     * @return $this
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
     * @param null|int|\Zend_Db_Expr $customer
     * @param int|Zend_Db_Expr $website
     * @return Select
     */
    public function getConditionsSql($customer, $website)
    {
        $table = $this->getResource()->getTable('wishlist');

        $select = $this->getResource()->createSelect();
        $select->from(array('list' => $table), array(new \Zend_Db_Expr(1)));
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
