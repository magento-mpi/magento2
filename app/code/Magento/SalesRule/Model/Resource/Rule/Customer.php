<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * SalesRule Rule Customer Model Resource
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\SalesRule\Model\Resource\Rule;

class Customer extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * constructor
     *
     */
    protected function _construct()
    {
        $this->_init('salesrule_customer', 'rule_customer_id');
    }

    /**
     * Get rule usage record for a customer
     *
     * @param \Magento\SalesRule\Model\Rule\Customer $rule
     * @param int $customerId
     * @param int $ruleId
     * @return \Magento\SalesRule\Model\Resource\Rule\Customer
     */
    public function loadByCustomerRule($rule, $customerId, $ruleId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getMainTable())
            ->where('customer_id = :customer_id')
            ->where('rule_id = :rule_id');
        $data = $read->fetchRow($select, array(':rule_id' => $ruleId, ':customer_id' => $customerId));
        if (false === $data) {
            // set empty data, as an existing rule object might be used
            $data = array();
        }
        $rule->setData($data);
        return $this;
    }
}
