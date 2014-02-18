<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Plugin;

class QuoteItemProductOption
{
    /**
     * @param \Magento\Sales\Model\Convert\Quote $subject
     * @param callable $proceed
     * @param \Magento\Sales\Model\Quote\Item\AbstractItem $item
     *
     * @return \Magento\Sales\Model\Order\Item
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundItemToOrderItem(
        \Magento\Sales\Model\Convert\Quote $subject,
        \Closure $proceed,
        \Magento\Sales\Model\Quote\Item\AbstractItem $item
    ) {
        /** @var $orderItem \Magento\Sales\Model\Order\Item */
        $orderItem = $proceed($item);
        /** @var $quoteItem \Magento\Sales\Model\Quote\Item */
        $quoteItem = reset($arguments);

        if (is_array($quoteItem->getOptions())) {
            foreach ($quoteItem->getOptions() as $itemOption) {
                $code = explode('_', $itemOption->getCode());
                if (isset($code[1]) && is_numeric($code[1])) {
                    $option = $quoteItem->getProduct()->getOptionById($code[1]);
                    if ($option && $option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_FILE) {
                        try {
                            $option->groupFactory($option->getType())
                                ->setQuoteItemOption($itemOption)
                                ->copyQuoteToOrder();

                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                }
            }
        }
        return $orderItem;
    }
}
