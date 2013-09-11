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
 * @method \Magento\SalesRule\Model\Resource\Coupon _getResource()
 * @method \Magento\SalesRule\Model\Resource\Coupon getResource()
 * @method int getRuleId()
 * @method \Magento\SalesRule\Model\Coupon setRuleId(int $value)
 * @method string getCode()
 * @method \Magento\SalesRule\Model\Coupon setCode(string $value)
 * @method int getUsageLimit()
 * @method \Magento\SalesRule\Model\Coupon setUsageLimit(int $value)
 * @method int getUsagePerCustomer()
 * @method \Magento\SalesRule\Model\Coupon setUsagePerCustomer(int $value)
 * @method int getTimesUsed()
 * @method \Magento\SalesRule\Model\Coupon setTimesUsed(int $value)
 * @method string getExpirationDate()
 * @method \Magento\SalesRule\Model\Coupon setExpirationDate(string $value)
 * @method int getIsPrimary()
 * @method \Magento\SalesRule\Model\Coupon setIsPrimary(int $value)
 * @method int getType()
 * @method \Magento\SalesRule\Model\Coupon setType(int $value)
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\SalesRule\Model;

class Coupon extends \Magento\Core\Model\AbstractModel
{
    /**
     * Coupon's owner rule instance
     *
     * @var \Magento\SalesRule\Model\Rule
     */
    protected $_rule;

    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento\SalesRule\Model\Resource\Coupon');
    }

    /**
     * Processing object before save data
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _beforeSave()
    {
        if (!$this->getRuleId() && $this->_rule instanceof \Magento\SalesRule\Model\Rule) {
            $this->setRuleId($this->_rule->getId());
        }
        return parent::_beforeSave();
    }

    /**
     * Set rule instance
     *
     * @param  \Magento\SalesRule\Model\Rule
     * @return \Magento\SalesRule\Model\Coupon
     */
    public function setRule(\Magento\SalesRule\Model\Rule $rule)
    {
        $this->_rule = $rule;
        return $this;
    }

    /**
     * Load primary coupon for specified rule
     *
     * @param \Magento\SalesRule\Model\Rule|int $rule
     * @return \Magento\SalesRule\Model\Coupon
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
     * @return \Magento\SalesRule\Model\Coupon
     */
    public function loadByCode($couponCode)
    {
        $this->load($couponCode, 'code');
        return $this;
    }
}
