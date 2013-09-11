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
 * SalesRule Rule Customer Model
 *
 * @method \Magento\SalesRule\Model\Resource\Rule\Customer _getResource()
 * @method \Magento\SalesRule\Model\Resource\Rule\Customer getResource()
 * @method int getRuleId()
 * @method \Magento\SalesRule\Model\Rule\Customer setRuleId(int $value)
 * @method int getCustomerId()
 * @method \Magento\SalesRule\Model\Rule\Customer setCustomerId(int $value)
 * @method int getTimesUsed()
 * @method \Magento\SalesRule\Model\Rule\Customer setTimesUsed(int $value)
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\SalesRule\Model\Rule;

class Customer extends \Magento\Core\Model\AbstractModel
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('\Magento\SalesRule\Model\Resource\Rule\Customer');
    }
    
    public function loadByCustomerRule($customerId, $ruleId)
    {
        $this->_getResource()->loadByCustomerRule($this, $customerId, $ruleId);
        return $this;
    }
}
