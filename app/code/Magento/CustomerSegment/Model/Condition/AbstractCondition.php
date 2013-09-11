<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Model\Condition;

class AbstractCondition extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return array
     */
    public function getMatchedEvents()
    {
        return array();
    }

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
            $this->_defaultOperatorInputByType['multiselect'] = array('==', '!=', '[]', '![]');

        }
        return $this->_defaultOperatorInputByType;
    }

    /**
     * Default operator options getter
     * Provides all possible operator options
     *
     * @return array
     */
    public function getDefaultOperatorOptions()
    {
        if (null === $this->_defaultOperatorOptions) {
            $this->_defaultOperatorOptions = parent::getDefaultOperatorOptions();

            $this->_defaultOperatorOptions['[]'] = __('contains');
            $this->_defaultOperatorOptions['![]'] = __('does not contains');
        }
        return $this->_defaultOperatorOptions;
    }

    /**
     * Get condition combine resource model
     *
     * @return \Magento\CustomerSegment\Model\Resource\Segment
     */
    public function getResource()
    {
        return \Mage::getResourceSingleton('Magento\CustomerSegment\Model\Resource\Segment');
    }

    /**
     * Generate customer condition string
     *
     * @param mixed $customer
     * @param string $fieldName
     * @return string
     */
    protected function _createCustomerFilter($customer, $fieldName)
    {
        $customerFilter = '';
        if ($customer) {
            $customerFilter = "{$fieldName} = :customer_id";
        } else {
            $customerFilter = "{$fieldName} = root.entity_id";
        }

        return $customerFilter;
    }

    /**
     * Limit select by website with joining to store table
     *
     * @param \Zend_Db_Select $select
     * @param int | \Zend_Db_Expr $website
     * @param string $storeIdField
     * @return \Magento\CustomerSegment\Model\Condition\AbstractCondition
     */
    protected function _limitByStoreWebsite(\Zend_Db_Select $select, $website, $storeIdField)
    {
        $storeTable = $this->getResource()->getTable('core_store');
        $select->join(array('store'=> $storeTable), $storeIdField.'=store.store_id', array())
            ->where('store.website_id=?', $website);
        return $this;
    }
}
