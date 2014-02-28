<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Model\Rule\Action\Discount;

class ToFixed extends AbstractDiscount
{
    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param \Magento\Sales\Model\Quote\Item\AbstractItem $item
     * @param float $qty
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\Data
     */
    public function calculate($rule, $item, $qty)
    {
        /** @var \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData */
        $discountData = $this->discountFactory->create();

        $store = $item->getQuote()->getStore();

        $itemPrice              = $this->validator->getItemPrice($item);
        $baseItemPrice          = $this->validator->getItemBasePrice($item);
        $itemOriginalPrice      = $this->validator->getItemOriginalPrice($item);
        $baseItemOriginalPrice  = $this->validator->getItemBaseOriginalPrice($item);

        $quoteAmount = $store->convertPrice($rule->getDiscountAmount());

        $discountData->setAmount($qty * ($itemPrice - $quoteAmount));
        $discountData->setBaseAmount($qty * ($baseItemPrice - $rule->getDiscountAmount()));
        $discountData->setOriginalAmount($qty * ($itemOriginalPrice - $quoteAmount));
        $discountData->setBaseOriginalAmount($qty * ($baseItemOriginalPrice - $rule->getDiscountAmount()));

        return $discountData;

    }
}
