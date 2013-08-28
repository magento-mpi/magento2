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
 * SalesRule Coupon Model
 *
 * @method Magento_SalesRule_Model_Resource_Coupon _getResource()
 * @method Magento_SalesRule_Model_Resource_Coupon getResource()
 * @method int getRuleId()
 * @method Magento_SalesRule_Model_Coupon setRuleId(int $value)
 * @method string getCode()
 * @method Magento_SalesRule_Model_Coupon setCode(string $value)
 * @method int getUsageLimit()
 * @method Magento_SalesRule_Model_Coupon setUsageLimit(int $value)
 * @method int getUsagePerCustomer()
 * @method Magento_SalesRule_Model_Coupon setUsagePerCustomer(int $value)
 * @method int getTimesUsed()
 * @method Magento_SalesRule_Model_Coupon setTimesUsed(int $value)
 * @method string getExpirationDate()
 * @method Magento_SalesRule_Model_Coupon setExpirationDate(string $value)
 * @method int getIsPrimary()
 * @method Magento_SalesRule_Model_Coupon setIsPrimary(int $value)
 * @method int getType()
 * @method Magento_SalesRule_Model_Coupon setType(int $value)
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_SalesRule_Model_Coupon extends Magento_Core_Model_Abstract
{
    /**
     * Coupon's owner rule instance
     *
     * @var Magento_SalesRule_Model_Rule
     */
    protected $_rule;

    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento_SalesRule_Model_Resource_Coupon');
    }

    /**
     * Processing object before save data
     *
     * @return Magento_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        if (!$this->getRuleId() && $this->_rule instanceof Magento_SalesRule_Model_Rule) {
            $this->setRuleId($this->_rule->getId());
        }
        return parent::_beforeSave();
    }

    /**
     * Set rule instance
     *
     * @param  Magento_SalesRule_Model_Rule
     * @return Magento_SalesRule_Model_Coupon
     */
    public function setRule(Magento_SalesRule_Model_Rule $rule)
    {
        $this->_rule = $rule;
        return $this;
    }

    /**
     * Load primary coupon for specified rule
     *
     * @param Magento_SalesRule_Model_Rule|int $rule
     * @return Magento_SalesRule_Model_Coupon
     */
    public function loadPrimaryByRule($rule)
    {
        $this->getResource()->loadPrimaryByRule($this, $rule);
        return $this;
    }

    /**
     * Load Shopping Cart Price Rule by coupon code
     *
     * @param string $couponCode
     * @return Magento_SalesRule_Model_Coupon
     */
    public function loadByCode($couponCode)
    {
        $this->load($couponCode, 'code');
        return $this;
    }
}
