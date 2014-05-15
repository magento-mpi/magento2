<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Test\Constraint;

use Magento\Catalog\Test\Constraint\AssertProductForm;
use Mtf\Fixture\InjectableFixture;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Page\Product\CatalogProductEdit;

/**
 * Class AssertDownloadableProductForm
 * Assert that downloadable product data on edit page equals to passed from fixture
 */
class AssertDownloadableProductForm extends AssertProductForm
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert form data equals fixture data
     *
     * @param InjectableFixture $product
     * @param CatalogProductIndex $productGrid
     * @param CatalogProductEdit $productPage
     * @return void
     */
    public function processAssert(
        InjectableFixture $product,
        CatalogProductIndex $productGrid,
        CatalogProductEdit $productPage
    ) {
        $filter = ['sku' => $product->getData('sku')];
        $productGrid->open()->getProductGrid()->searchAndOpen($filter);
        $fields = $this->convertArray($product->getData());
        $fieldsForm = $productPage->getProductBlockForm()->getData($product);
        \PHPUnit_Framework_Assert::assertEquals($fields, $fieldsForm, 'Form data not equals fixture data');
    }

    /**
     * Invalidate fixture array
     *
     * @param array $fields
     * @return array
     */
    public function convertArray(array $fields)
    {
        foreach ($fields as $key => $value) {
            if (is_array($value)) {
                $fields[$key] = $this->convertArray($value);
            } else {
                if ($key == "sample_type" || $key == "file_type") {
                    $fields[$fields[$key] == 'url' ? $key . '_url' : $key . '_file'] = 1;
                    unset($fields[$key]);
                }
            }
        }
        return parent::convertArray($fields);
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
