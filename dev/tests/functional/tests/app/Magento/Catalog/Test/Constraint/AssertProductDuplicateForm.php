<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;

/**
 * Class AssertProductDuplicateForm
 */
class AssertProductDuplicateForm extends AssertProductForm
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

        $errors = $this->compareArray($fixtureData, $formData);
        \PHPUnit_Framework_Assert::assertEmpty(
            $errors,
            "These data must be equal to each other:\n" . implode("\n", $errors)
        );
    }

    /**
     * Prepares and returns data to the fixture, ready for comparison
     *
     * @param FixtureInterface $product
     * @return array
     */
    protected function prepareFixtureData(FixtureInterface $product)
    {
        $compareData = $product->getData();
        $compareData = array_filter($compareData);

        array_walk_recursive(
            $compareData,
            function (&$item, $key, $formattingOptions) {
                if (isset($formattingOptions[$key])) {
                    $item = number_format(
                        $item,
                        $formattingOptions[$key]['decimals'],
                        $formattingOptions[$key]['dec_point'],
                        $formattingOptions[$key]['thousands_sep']
                    );
                }
            },
            $this->formattingOptions
        );

        if (isset($compareData['status'])) {
            $compareData['status'] = 'Product offline';
        }
        if (isset($compareData['quantity_and_stock_status']['qty'])) {
            $compareData['quantity_and_stock_status']['qty'] = '';
        }
        if (isset($compareData['special_price'])) {
            $compareData['special_price'] = ['special_price' => $compareData['special_price']];
        }
        $compareData['sku'] .= '-1';
        $compareData['quantity_and_stock_status']['is_in_stock'] = 'Out of Stock';
        unset($compareData['category_ids'], $compareData['id']);

        return $compareData;
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Form data equals to fixture data of duplicated product.';
    }
}
