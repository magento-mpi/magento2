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
 * Abstract class for rule condition
 */
namespace Magento\Reminder\Model\Condition;

class AbstractCondition extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * Customize default operator input by type mapper for some types
     * @return array
     */
    public function getDefaultOperatorInputByType()
    {
        if (null === $this->_defaultOperatorInputByType) {
            parent::getDefaultOperatorInputByType();
            $this->_defaultOperatorInputByType['numeric'] = array('==', '!=', '>=', '>', '<=', '<');
            $this->_defaultOperatorInputByType['string'] = array('==', '!=', '{}', '!{}');
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
        return \Mage::getResourceSingleton('Magento\Reminder\Model\Resource\Rule');
    }

    /**
     * Generate customer condition string
     *
     * @param $customer
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
     * @param int | \Zend_Db_Expr $website
     * @param string $storeIdField
     * @return \Magento\Reminder\Model\Condition\AbstractCondition
     */
    protected function _limitByStoreWebsite(\Zend_Db_Select $select, $website, $storeIdField)
    {
        $storeTable = $this->getResource()->getTable('core_store');
        $select->join(array('store' => $storeTable), $storeIdField . '=store.store_id', array())
            ->where('store.website_id=?', $website);
        return $this;
    }
}
