<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Test\Constraint;

use Magento\Catalog\Test\Constraint\AssertProductForm;
use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;

/**
 * Class AssertDownloadableProductForm
 */
class AssertDownloadableProductForm extends AssertProductForm
{
    protected $sortFields = ['downloadable_links/downloadable/link::sort_order'];

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that downloadable product data on edit page equals to passed from fixture
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
        $filter = ['sku' => $product->getData('sku')];
        $productGrid->open()->getProductGrid()->searchAndOpen($filter);

        $fieldsFixture = $this->prepareFixtureData($product->getData(), $this->sortFields);
        $fieldsForm = $this->prepareFormData($productPage->getForm()->getData($product), $this->sortFields);
        $error = $this->verifyData($fieldsFixture, $fieldsForm);
        \PHPUnit_Framework_Assert::assertTrue(null === $error, $error);
    }

    /**
     * Text of Visible in product form assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Form data equal the fixture data.';
    }
}
