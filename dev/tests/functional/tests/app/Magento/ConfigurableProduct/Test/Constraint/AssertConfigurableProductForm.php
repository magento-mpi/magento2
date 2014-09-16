<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Constraint\AssertProductForm;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;

/**
 * Class AssertConfigurableProductForm
 * Assert that displayed product data on edit page equals passed from fixture
 */
class AssertConfigurableProductForm extends AssertProductForm
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that displayed product data on edit page equals passed from fixture
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
        $productGrid->open()->getProductGrid()->searchAndOpen(['sku' => $product->getSku()]);

        $form = $productPage->getForm();
        $formData = $form->getData($product);
        foreach (array_keys($formData['configurable_attributes_data']['matrix']) as $key) {
            unset($formData['configurable_attributes_data']['matrix'][$key]['price']);
        }

        $fixtureData = $this->prepareFixtureData($product->getData(), $product);
        $attributes = $fixtureData['configurable_attributes_data']['attributes_data'];
        $matrix = $fixtureData['configurable_attributes_data']['matrix'];
        unset($fixtureData['configurable_attributes_data'], $fixtureData['id']);

        $fixtureData['configurable_attributes_data']['attributes_data'] = $this->prepareAttributes($attributes);
        $fixtureData['configurable_attributes_data']['matrix'] = $this->prepareMatrix($matrix);

        $errors = $this->verifyData($fixtureData, $formData);
        \PHPUnit_Framework_Assert::assertEmpty($errors, $errors);
    }

    /**
     * Preparing data attributes fixture
     *
     * @param array $fixtureAttribute
     * @return array
     */
    protected function prepareAttributes(array $fixtureAttribute)
    {
        foreach ($fixtureAttribute as &$attribute) {
            unset($attribute['id'], $attribute['label'], $attribute['code']);
            foreach ($attribute['options'] as &$option) {
                $option['pricing_value'] = number_format($option['pricing_value'], 4);
                unset($option['id']);
            }
        }

        return $fixtureAttribute;
    }

    /**
     * Preparing data matrix fixture
     *
     * @param array $fixtureMatrix
     * @return array
     */
    protected function prepareMatrix(array $fixtureMatrix)
    {
        foreach ($fixtureMatrix as &$matrix) {
            $matrix['display'] = 'Yes';
            unset($matrix['configurable_attribute'], $matrix['associated_product_ids']);
        }

        return $fixtureMatrix;
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Form data equal the configurable product data.';
    }
}
