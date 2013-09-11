<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Root segment condition (top level condition)
 */
namespace Magento\CustomerSegment\Model\Segment\Condition\Combine;

class Root
    extends \Magento\CustomerSegment\Model\Segment\Condition\Combine
{
    /**
     * @var \Magento\Customer\Model\Config\Share
     */
    protected $_configShare;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Customer\Model\Config\Share $configShare
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Customer\Model\Config\Share $configShare,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->setType('\Magento\CustomerSegment\Model\Segment\Condition\Combine\Root');
        $this->_configShare = $configShare;
    }

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return array
     */
    public function getMatchedEvents()
    {
        return array('customer_login');
    }

    /**
     * Prepare filter condition by customer
     *
     * @param int|array|\Magento\Customer\Model\Customer|Zend_Db_Select $customer
     * @param string $fieldName
     * @return string
     */
    protected function _createCustomerFilter($customer, $fieldName)
    {
        if ($customer instanceof \Magento\Customer\Model\Customer) {
            $customer = $customer->getId();
        } else if ($customer instanceof \Zend_Db_Select) {
            $customer = new \Zend_Db_Expr($customer);
        }

        return $this->getResource()->quoteInto("{$fieldName} IN (?)", $customer);
    }

    /**
     * Prepare base select with limitation by customer
     *
     * @param   null | array | int | \Magento\Customer\Model\Customer $customer
     * @param   int | \Zend_Db_Expr $website
     * @return  \Magento\DB\Select
     */
    protected function _prepareConditionsSql($customer, $website)
    {
        $select = $this->getResource()->createSelect();
        $table = array('root' => $this->getResource()->getTable('customer_entity'));

        if ($customer) {
            // For existing customer
            $select->from($table, new \Zend_Db_Expr(1));
        } else {
            $select->from($table, array('entity_id', 'website_id'));
            if ($customer === null && $this->_configShare->isWebsiteScope()) {
                $select->where('website_id=?', $website);
            }
        }

        return $select;
    }
}
