<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Constraint\AssertProductDuplicateForm;

/**
 * Class AssertGiftCardDuplicateForm
 */
class AssertGiftCardDuplicateForm extends AssertProductDuplicateForm
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert form data equals duplicate product gift card data
     *
     * @param FixtureInterface $product
     * @param CatalogProductIndex $productGrid
     * @param CatalogProductEdit $productPage
     * @return void
     */
    public function processAssert(
        FixtureInterface $product,
        CatalogProductIndex $productGrid,
        CatalogProductEdit $productPage
    ) {
        $filter = ['sku' => $product->getSku() . '-1'];
        $productGrid->open()->getProductGrid()->searchAndOpen($filter);

        $formData = $productPage->getForm()->getData($product);
        $fixtureData = $this->prepareFixtureData($product);
        $giftcardAmounts = [];
        foreach ($fixtureData['giftcard_amounts'] as $key => $amount) {
            $giftcardAmounts[$key + 1]['price'] = $amount['price'];
        }
        $fixtureData['giftcard_amounts'] = $giftcardAmounts;

        $errors = $this->compareArray($fixtureData, $formData);
        \PHPUnit_Framework_Assert::assertEmpty(
            $errors,
            "Duplicated gift card data is not equal to expected:\n" . implode("\n", $errors)
        );
    }
}
