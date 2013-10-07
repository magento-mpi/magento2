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
 * Customer wishlist conditions combine
 */
namespace Magento\Reminder\Model\Rule\Condition;

class Wishlist
    extends \Magento\Reminder\Model\Condition\Combine\AbstractCombine
{
    /**
     * Core Date
     *
     * @var \Magento\Core\Model\Date
     */
    protected $_coreDate;

    /**
     * Core resource helper
     *
     * @var \Magento\Reminder\Model\Resource\HelperFactory
     */
    protected $_resHelperFactory;

    /**
     * Wishlist Combine Factory
     *
     * @var \Magento\Reminder\Model\Rule\Condition\Wishlist\CombineFactory
     */
    protected $_combineFactory;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Reminder\Model\Resource\Rule $ruleResource
     * @param \Magento\Core\Model\Date $coreDate
     * @param \Magento\Reminder\Model\Resource\HelperFactory $resHelperFactory
     * @param \Magento\Reminder\Model\Rule\Condition\Wishlist\CombineFactory $combineFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Reminder\Model\Resource\Rule $ruleResource,
        \Magento\Core\Model\Date $coreDate,
        \Magento\Reminder\Model\Resource\HelperFactory $resHelperFactory,
        \Magento\Reminder\Model\Rule\Condition\Wishlist\CombineFactory $combineFactory,
        array $data = array()
    ) {
        parent::__construct($context, $ruleResource, $data);
        $this->setType('Magento\Reminder\Model\Rule\Condition\Wishlist');
        $this->setValue(null);
        $this->_coreDate = $coreDate;
        $this->_resHelperFactory = $resHelperFactory;
        $this->_combineFactory = $combineFactory;
    }

    /**
     * Get list of available subconditions
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return $this->_combineFactory->create()->getNewChildSelectOptions();
    }

    /**
     * Get input type for attribute value
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * Override parent method
     *
     * @return \Magento\Reminder\Model\Rule\Condition\Wishlist
     */
    public function loadValueOptions()
    {
        $this->setValueOption(array());
        return $this;
    }

    /**
     * Prepare operator select options
     *
     * @return \Magento\Reminder\Model\Rule\Condition\Wishlist
     */
    public function loadOperatorOptions()
    {
        $this->setOperatorOption(array(
            '==' => __('for'),
            '>'  => __('for greater than'),
            '>=' => __('for or greater than')
        ));
        return $this;
    }

    /**
     * Return required validation
     *
     * @return true
     */
    protected function _getRequiredValidation()
    {
        return true;
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . __('The wish list is not empty and abandoned %1 %2 days and %3 of these conditions match:',
                $this->getOperatorElementHtml(), $this->getValueElementHtml(), $this->getAggregatorElement()->getHtml())
            . $this->getRemoveLinkHtml();
    }

    /**
     * Get condition SQL select
     *
     * @param $customer
     * @param $website
     * @return \Magento\DB\Select
     * @throws \Magento\Core\Exception
     */
    protected function _prepareConditionsSql($customer, $website)
    {
        $conditionValue = (int)$this->getValue();
        if ($conditionValue < 1) {
            throw new \Magento\Core\Exception(
                __('The root wish list condition should have a days value of 1 or greater.')
            );
        }

        $wishlistTable = $this->getResource()->getTable('wishlist');
        $wishlistItemTable = $this->getResource()->getTable('wishlist_item');
        $operator = $this->getResource()->getSqlOperator($this->getOperator());

        $select = $this->getResource()->createSelect();
        $select->from(array('item' => $wishlistItemTable), array(new \Zend_Db_Expr(1)));

        $select->joinInner(
            array('list' => $wishlistTable),
            'item.wishlist_id = list.wishlist_id',
            array()
        );

        $this->_limitByStoreWebsite($select, $website, 'item.store_id');

        $currentTime = $this->_coreDate->gmtDate();
        /** @var \Magento\Core\Model\Resource\Helper $daysDiffSql */
        $daysDiffSql = $this->_resHelperFactory->create();
        $daysDiffSql->getDateDiff('list.updated_at', $select->getAdapter()->formatDate($currentTime));
        $select->where($this->_resHelperFactory . " {$operator} ?", $conditionValue);
        $select->where($this->_createCustomerFilter($customer, 'list.customer_id'));
        $select->limit(1);
        return $select;
    }

    /**
     * Get base SQL select
     *
     * @param $customer
     * @param $website
     * @return \Magento\DB\Select
     */
    public function getConditionsSql($customer, $website)
    {
        $select     = $this->_prepareConditionsSql($customer, $website);
        $required   = $this->_getRequiredValidation();
        $aggregator = ($this->getAggregator() == 'all') ? ' AND ' : ' OR ';
        $operator   = $required ? '=' : '<>';
        $conditions = array();

        foreach ($this->getConditions() as $condition) {
            $sql = $condition->getConditionsSql($customer, $website);
            if ($sql) {
                $conditions[] = "(" . $select->getAdapter()->getIfNullSql("(" . $sql . ")", 0) . " {$operator} 1)";
            }
        }

        if (!empty($conditions)) {
            $select->where(implode($aggregator, $conditions));
        }

        return $select;
    }
}
