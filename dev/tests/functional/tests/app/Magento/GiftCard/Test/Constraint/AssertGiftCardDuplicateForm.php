<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftCard\Test\Constraint;

use Magento\Catalog\Test\Constraint\AssertProductDuplicateForm;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Mtf\Fixture\FixtureInterface;

/**
 * Assert form data equals fixture data.
 */
class AssertGiftCardDuplicateForm extends AssertProductDuplicateForm
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert form data equals duplicate gift card data.
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
        $productGrid->open();
        $productGrid->getProductGrid()->searchAndOpen($filter);

        $formData = $productPage->getProductForm()->getData($product);
        $fixtureData = $this->prepareFixtureData($product->getData());

        $errors = $this->verifyData($fixtureData, $formData);
        \PHPUnit_Framework_Assert::assertEmpty($errors, $errors);
    }
}
