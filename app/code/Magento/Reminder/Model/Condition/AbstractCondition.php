<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reminder\Model\Condition;

/**
 * Abstract class for rule condition
 */
class AbstractCondition extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * Rule Resource
     *
     * @var \Magento\Reminder\Model\Resource\Rule
     */
    protected $_ruleResource;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Reminder\Model\Resource\Rule $ruleResource
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Reminder\Model\Resource\Rule $ruleResource,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_ruleResource = $ruleResource;
    }

    /**
     * Customize default operator input by type mapper for some types
     *
     * @return array|null
     */
    public function getDefaultOperatorInputByType()
    {
        if (null === $this->_defaultOperatorInputByType) {
            parent::getDefaultOperatorInputByType();
            $this->_defaultOperatorInputByType['numeric'] = ['==', '!=', '>=', '>', '<=', '<'];
            $this->_defaultOperatorInputByType['string'] = ['==', '!=', '{}', '!{}'];
        }
        return $this->_defaultOperatorInputByType;
    }

    /**
     * Get condition combine resource model
     *
     * @return \Magento\Reminder\Model\Resource\Rule
     */
    public function getResource()
    {
        return $this->_ruleResource;
    }

    /**
     * Generate customer condition string
     *
     * @param null|int|\Zend_Db_Expr $customer
     * @param string $fieldName
     * @return string
     */
    protected function _createCustomerFilter($customer, $fieldName)
    {
        return "{$fieldName} = root.entity_id";
    }

    /**
     * Limit select by website with joining to store table
     *
     * @param \Zend_Db_Select $select
     * @param int|\Zend_Db_Expr $website
     * @param string $storeIdField
     * @return $this
     */
    protected function _limitByStoreWebsite(\Zend_Db_Select $select, $website, $storeIdField)
    {
        $storeTable = $this->getResource()->getTable('store');
        $select->join(
            ['store' => $storeTable],
            $storeIdField . '=store.store_id',
            []
        )->where(
            'store.website_id=?',
            $website
        );
        return $this;
    }
}
