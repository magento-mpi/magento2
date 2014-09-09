<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Data;

use \Magento\Sales\Model\Quote;

/**
 * Cart mapper
 */
class CartMapper
{
    /**
     * Fetch base quote data and map it to DTO fields
     *
     * @param Quote $quote
     * @return array
     */
    public function map(Quote $quote)
    {
        return [
            Cart::ID => $quote->getId(),
            Cart::STORE_ID  => $quote->getStoreId(),
            Cart::CREATED_AT  => $quote->getCreatedAt(),
            Cart::UPDATED_AT  => $quote->getUpdatedAt(),
            Cart::CONVERTED_AT => $quote->getConvertedAt(),
            Cart::IS_ACTIVE => $quote->getIsActive(),
            Cart::IS_VIRTUAL => $quote->getIsVirtual(),
            Cart::ITEMS_COUNT => $quote->getItemsCount(),
            Cart::ITEMS_QUANTITY => $quote->getItemsQty(),
            Cart::CHECKOUT_METHOD => $quote->getCheckoutMethod(),
            Cart::RESERVED_ORDER_ID => $quote->getReservedOrderId(),
            Cart::ORIG_ORDER_ID => $quote->getOrigOrderId(),
        ];
    }
}
