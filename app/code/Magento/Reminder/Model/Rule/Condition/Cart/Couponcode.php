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
 * Cart coupon code condition
 */
class Magento_Reminder_Model_Rule_Condition_Cart_Couponcode
    extends Magento_Reminder_Model_Condition_Abstract
{
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
        $this->setType('Magento_Reminder_Model_Rule_Condition_Cart_Couponcode');
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
            'label' => __('Coupon Code'));
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . __('Shopping cart %1 a coupon applied', $this->getValueElementHtml())
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
     * @return Magento_Reminder_Model_Rule_Condition_Cart_Couponcode
     */
    public function loadValueOptions()
    {
        $this->setValueOption(array(
            '1' => __('has'),
            '0' => __('does not have')
        ));
        return $this;
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
        $table = $this->getResource()->getTable('sales_flat_quote');
        $inversion = ((int)$this->getValue() ? '' : 'NOT');

        $select = $this->getResource()->createSelect();
        $select->from(array('quote' => $table), array(new Zend_Db_Expr(1)));

        $this->_limitByStoreWebsite($select, $website, 'quote.store_id');
        $select->where('quote.is_active = 1');
        $select->where("{$inversion} (" . "quote.coupon_code IS NOT NULL AND quote.coupon_code <> "
            . $select->getAdapter()->quote('') . ")");
        $select->where($this->_createCustomerFilter($customer, 'quote.customer_id'));
        $select->limit(1);
        return $select;
    }
}
