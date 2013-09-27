<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_CustomerSegment_Model_Condition_Abstract extends Magento_Rule_Model_Condition_Abstract
{
    /**
     * @var Magento_CustomerSegment_Model_Resource_Segment
     */
    protected $_resourceSegment;

    /**
     * @param Magento_CustomerSegment_Model_Resource_Segment $resourceSegment
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_CustomerSegment_Model_Resource_Segment $resourceSegment,
        Magento_Rule_Model_Condition_Context $context,
        array $data = array()
    ) {
        $this->_resourceSegment = $resourceSegment;
        parent::__construct($context, $data);
    }

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
     * @return Magento_CustomerSegment_Model_Resource_Segment
     */
    public function getResource()
    {
        return $this->_resourceSegment;
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
     * @param Zend_Db_Select $select
     * @param int | Zend_Db_Expr $website
     * @param string $storeIdField
     * @return Magento_CustomerSegment_Model_Condition_Abstract
     */
    protected function _limitByStoreWebsite(Zend_Db_Select $select, $website, $storeIdField)
    {
        $storeTable = $this->getResource()->getTable('core_store');
        $select->join(array('store'=> $storeTable), $storeIdField.'=store.store_id', array())
            ->where('store.website_id=?', $website);
        return $this;
    }
}
