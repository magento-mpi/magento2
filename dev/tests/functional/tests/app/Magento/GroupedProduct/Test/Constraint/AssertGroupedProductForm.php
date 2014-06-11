<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Mtf\Fixture\InjectableFixture;
use Magento\Catalog\Test\Constraint\AssertProductForm;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;

/**
 * Class AssertGroupedProductForm
 */
class AssertGroupedProductForm extends AssertProductForm
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
        $filter = ['sku' => $product->getData('sku')];
        $productGrid->open()->getProductGrid()->searchAndOpen($filter);
        $fieldsForm = $productPage->getForm()->getData($product);
        $fieldsFixture = $this->prepareFixtureData($product);
        $fieldsFixture['grouped_products'] = $this->prepareGroupedOptions($fieldsFixture['grouped_products']);

        \PHPUnit_Framework_Assert::assertEquals($fieldsFixture, $fieldsForm, 'Form data not equals fixture data');
    }

    /**
     * Prepare Grouped Options array from preset
     *
     * @param array $fields
     * @return array|null
     */
    protected function prepareGroupedOptions(array $fields)
    {
        if (!isset($fields['preset'])) {
            return $fields;
        }
        $preset = $fields['preset']['assigned_products'];
        $products = $fields['products'];
        foreach ($preset as $productIncrement => & $item) {
            if (!isset($products[$productIncrement])) {
                break;
            }
            /** @var InjectableFixture $fixture */
            $fixture = $products[$productIncrement];
            $item['search_data']['sku'] = $fixture->getData('sku');
        }

        return $preset;
    }
}
