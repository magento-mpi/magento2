<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Catalog_Model_Plugin_QuoteItemProductOption
{
    /**
     * @param array $arguments
     * @param Magento_Code_Plugin_InvocationChain $invocationChain
     * @return Magento_Sales_Model_Order_Item
     */
    public function aroundItemToOrderItem(array $arguments, Magento_Code_Plugin_InvocationChain $invocationChain)
    {
        /** @var $orderItem Magento_Sales_Model_Order_Item */
        $orderItem = $invocationChain->proceed($arguments);
        /** @var $quoteItem Magento_Sales_Model_Quote_Item */
        $quoteItem = reset($arguments);

        if (is_array($quoteItem->getOptions())) {
            foreach ($quoteItem->getOptions() as $itemOption) {
                $code = explode('_', $itemOption->getCode());
                if (isset($code[1]) && is_numeric($code[1])) {
                    $option = $quoteItem->getProduct()->getOptionById($code[1]);
                    if ($option && $option->getType() == Magento_Catalog_Model_Product_Option::OPTION_TYPE_FILE) {
                        try {
                            $option->groupFactory($option->getType())
                                ->setQuoteItemOption($itemOption)
                                ->copyQuoteToOrder();

                        } catch (Exception $e) {
                            continue;
                        }
                    }
                }
            }
        }
        return $orderItem;
    }
}
