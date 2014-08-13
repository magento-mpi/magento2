<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Constraint;

use Mtf\Constraint\AbstractAssertForm;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\ConfigurableProduct\Test\Page\Adminhtml\CatalogProductEdit;
use Mtf\Fixture\FixtureInterface;

/**
 * Class AssertConfigurableProductDuplicateForm
 */
class AssertConfigurableProductDuplicateForm extends AbstractAssertForm
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert form data equals duplicate product configurable data
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

        $form = $productPage->getForm();
        $formData = $form->getData($product);
        foreach (array_keys($formData['configurable_attributes_data']['matrix']) as $key) {
            unset($formData['configurable_attributes_data']['matrix'][$key]['price']);
        }

        $fixtureData = $this->prepareFixtureData($product->getData());
        $attributes = $fixtureData['configurable_attributes_data']['attributes_data'];
        $matrix = $fixtureData['configurable_attributes_data']['matrix'];
        unset($fixtureData['configurable_attributes_data']);

        $fixtureData['configurable_attributes_data']['attributes_data'] = $this->prepareAttributes($attributes);
        $fixtureData['configurable_attributes_data']['matrix'] = $this->prepareMatrix($matrix);

        $errors = $this->verifyData($fixtureData, $formData);
        \PHPUnit_Framework_Assert::assertEmpty($errors, $errors);
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
