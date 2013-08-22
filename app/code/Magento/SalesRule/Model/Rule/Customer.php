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
 * @method Magento_SalesRule_Model_Resource_Rule_Customer _getResource()
 * @method Magento_SalesRule_Model_Resource_Rule_Customer getResource()
 * @method int getRuleId()
 * @method Magento_SalesRule_Model_Rule_Customer setRuleId(int $value)
 * @method int getCustomerId()
 * @method Magento_SalesRule_Model_Rule_Customer setCustomerId(int $value)
 * @method int getTimesUsed()
 * @method Magento_SalesRule_Model_Rule_Customer setTimesUsed(int $value)
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_SalesRule_Model_Rule_Customer extends Magento_Core_Model_Abstract 
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento_SalesRule_Model_Resource_Rule_Customer');
    }
    
    public function loadByCustomerRule($customerId, $ruleId)
    {
        $this->_getResource()->loadByCustomerRule($this, $customerId, $ruleId);
        return $this;
    }
}
