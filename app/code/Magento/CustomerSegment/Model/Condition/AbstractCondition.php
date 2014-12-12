<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerSegment\Model\Condition;

use Magento\Customer\Model\Customer;

class AbstractCondition extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * @var \Magento\CustomerSegment\Model\Resource\Segment
     */
    protected $_resourceSegment;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment,
        array $data = []
    ) {
        $this->_resourceSegment = $resourceSegment;
        parent::__construct($context, $data);
    }

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return string[]
     */
    public function getMatchedEvents()
    {
        return [];
    }

    /**
     * Customize default operator input by type mapper for some types
     * @return array
     */
    public function getDefaultOperatorInputByType()
    {
        if (null === $this->_defaultOperatorInputByType) {
            parent::getDefaultOperatorInputByType();
            $this->_defaultOperatorInputByType['numeric'] = ['==', '!=', '>=', '>', '<=', '<'];
            $this->_defaultOperatorInputByType['string'] = ['==', '!=', '{}', '!{}'];
            $this->_defaultOperatorInputByType['multiselect'] = ['==', '!=', '[]', '![]'];
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
        return $this->_resourceSegment;
    }

    /**
     * Generate customer condition string
     *
     * @param Customer|\Zend_Db_Expr $customer
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
