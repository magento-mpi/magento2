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
        $filter = ['sku' => $product->getSku()];
        $productGrid->open()->getProductGrid()->searchAndOpen($filter);
        $fieldsForm = $productPage->getForm()->getData($product);
        $fieldsFixture = $this->prepareFixtureData($product);
        $fieldsFixture['associated'] = $this->prepareGroupedOptions($fieldsFixture['associated']);

        $errors = $this->compareArray($fieldsFixture, $fieldsForm);
        \PHPUnit_Framework_Assert::assertTrue(
            empty($errors),
            "These data must be equal to each other:\n" . implode("\n", $errors)
        );
    }

    /**
     * Prepare Grouped Options array from preset
     *
     * @param array $fields
     * @return array|null
     */
    protected function prepareGroupedOptions(array $fields)
    {
        $result = [];
        foreach ($fields['assigned_products'] as $key => $item) {
            $result['assigned_products'][$key]['name'] = $item['name'];
            $result['assigned_products'][$key]['qty'] = $item['qty'];
        }

        return $result;
    }
}
