<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\TestCase;

use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductNew;
use Magento\GiftCard\Test\Fixture\GiftCardProduct;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for Create GiftCardProductEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create a Category.
 *
 * Steps:
 * 1. Login to the backend.
 * 2. Navigate to Products > Catalog.
 * 3. Start to create Gift Card Product.
 * 4. Fill in data according to data set.
 * 5. Save Product.
 * 6. Perform appropriate assertions.
 *
 * @group Gift_Card_(MX)
 * @ZephyrId MAGETWO-24997
 */
class CreateGiftCardProductEntityTest extends Injectable
{
    /**
     * Run create Gift Card product entity
     *
     * @param GiftCardProduct $product
     * @param CatalogProductIndex $productIndex
     * @param CatalogProductNew $productNew
     * @return void
     */
    public function test(
        GiftCardProduct $product,
        CatalogProductIndex $productIndex,
        CatalogProductNew $productNew
    ) {
        $productIndex->open();
        $productIndex->getGridPageActionBlock()->addProduct('giftcard');
        $productNew->getProductForm()->fill($product);
        $productNew->getFormPageActions()->save();
    }
}
