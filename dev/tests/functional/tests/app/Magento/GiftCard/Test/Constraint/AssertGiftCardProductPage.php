<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftCard\Test\Constraint;

use Magento\Catalog\Test\Constraint\AssertProductPage;

/**
 * Class AssertGiftCardProductPage
 * Assert that displayed product data on product page(front-end) equals passed from fixture.
 */
class AssertGiftCardProductPage extends AssertProductPage
{
    /**
     * Verify displayed product price on product page(front-end) equals passed from fixture
     *
     * @return string|null
     */
    protected function verifyPrice()
    {
        $productData = $this->product->getData();
        $priceOnPage = $this->productView->getPriceBlock()->getFinalPrice();
        $price = null;

        if (isset($productData['giftcard_amounts']) && 1 == count($productData['giftcard_amounts'])) {
            $amount = reset($productData['giftcard_amounts']);
            $price = $amount['price'];
        }

        if ($price == $priceOnPage) {
            return null;
        }
        return "Displayed product price on product page(front-end) not equals passed from fixture. "
        . "Actual: {$priceOnPage}, expected: {$price}.";
    }
}
