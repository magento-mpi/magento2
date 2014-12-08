<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Model\Segment\Condition\Combine;

use Magento\Customer\Model\Customer;
use Zend_Db_Expr;
use Zend_Db_Select;

/**
 * Root segment condition (top level condition)
 */
class Root extends \Magento\CustomerSegment\Model\Segment\Condition\Combine
{
    /**
     * @var \Magento\Customer\Model\Config\Share
     */
    protected $_configShare;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory
     * @param \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment
     * @param \Magento\Customer\Model\Config\Share $configShare
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory,
        \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment,
        \Magento\Customer\Model\Config\Share $configShare,
        array $data = []
    ) {
        parent::__construct($context, $conditionFactory, $resourceSegment, $data);
        $this->setType('Magento\CustomerSegment\Model\Segment\Condition\Combine\Root');
        $this->_configShare = $configShare;
    }

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return string[]
     */
    public function getMatchedEvents()
    {
        return ['customer_login'];
    }

    /**
     * Prepare filter condition by customer
     *
     * @param int|array|Customer|Zend_Db_Select $customer
     * @param string $fieldName
     * @return string
     */
    protected function _createCustomerFilter($customer, $fieldName)
    {
        if ($customer instanceof Customer) {
            $customer = $customer->getId();
        } elseif ($customer instanceof \Zend_Db_Select) {
            $customer = new Zend_Db_Expr($customer);
        }

        return $this->getResource()->quoteInto("{$fieldName} IN (?)", $customer);
    }

    /**
     * Prepare base select with limitation by customer
     *
     * @param   null|array|int|Customer $customer
     * @param   int|Zend_Db_Expr $website
     * @return  \Magento\Framework\DB\Select
     */
    protected function _prepareConditionsSql($customer, $website)
    {
        $select = $this->getResource()->createSelect();
        $table = ['root' => $this->getResource()->getTable('customer_entity')];

        if ($customer) {
            // For existing customer
            $select->from($table, new Zend_Db_Expr(1));
        } else {
            $select->from($table, ['entity_id', 'website_id']);
            if ($customer === null && $this->_configShare->isWebsiteScope()) {
                $select->where('website_id=?', $website);
            }
        }

        return $select;
    }
}
