<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Shopping Cart Rule data model
 *
 * @category    Magento
 * @package     Magento_OfflineShipping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\OfflineShipping\Model\SalesRule;

class Calculator extends \Magento\SalesRule\Model\Validator
{
    /**
     * Quote item free shipping ability check
     * This process not affect information about applied rules, coupon code etc.
     * This information will be added during discount amounts processing
     *
     * @param   \Magento\Sales\Model\Quote\Item\AbstractItem $item
     * @return  \Magento\OfflineShipping\Model\SalesRule\Calculator
     */
    public function processFreeShipping(\Magento\Sales\Model\Quote\Item\AbstractItem $item)
    {
        $address = $item->getAddress();
        $item->setFreeShipping(false);

        foreach ($this->_getRules() as $rule) {
            /* @var $rule \Magento\SalesRule\Model\Rule */
            if (!$this->_canProcessRule($rule, $address)) {
                continue;
            }

            if (!$rule->getActions()->validate($item)) {
                continue;
            }

            switch ($rule->getSimpleFreeShipping()) {
                case \Magento\OfflineShipping\Model\SalesRule\Rule::FREE_SHIPPING_ITEM:
                    $item->setFreeShipping($rule->getDiscountQty() ? $rule->getDiscountQty() : true);
                    break;

                case \Magento\OfflineShipping\Model\SalesRule\Rule::FREE_SHIPPING_ADDRESS:
                    $address->setFreeShipping(true);
                    break;
            }
            if ($rule->getStopRulesProcessing()) {
                break;
            }
        }
        return $this;
    }
}
